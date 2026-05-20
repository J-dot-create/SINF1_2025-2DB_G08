<?php
require_once __DIR__ . '/../dal/DataAccessLayer.php';

/**
 * Business Logic Layer (BLL)
 *
 * Esta classe concentra as regras de negocio da aplicacao.
 * A interface chama metodos desta classe, e a BLL usa a DAL para aceder
 * diretamente a base de dados.
 */
class BusinessLogicLayer {
    private $dal;

    /**
     * Cria a camada de acesso a dados que sera usada por todos os metodos.
     */
    public function __construct() {
        $this->dal = new DataAccessLayer();
    }

    /**
     * Obtem todos os eventos e junta informacao calculada para a listagem:
     * popularidade pela agenda pessoal e rating medio pelas avaliacoes.
     * A ordenacao pode ser por data, popularidade ou rating.
     */
    public function getAllEvents($sortBy = 'date') {
        $orderBy = "e.event_date ASC";

        if ($sortBy === 'popularity') {
            $orderBy = "popularity_count DESC, e.event_date ASC";
        } elseif ($sortBy === 'rating') {
            $orderBy = "average_rating DESC, rating_count DESC, e.event_date ASC";
        }

        $query = "SELECT
                    e.*,
                    COALESCE(pa_stats.popularity_count, 0) AS popularity_count,
                    COALESCE(rating_stats.average_rating, 0) AS average_rating,
                    COALESCE(rating_stats.rating_count, 0) AS rating_count
                  FROM event e
                  LEFT JOIN (
                      SELECT id_event, COUNT(*) AS popularity_count
                      FROM personalagenda
                      GROUP BY id_event
                  ) pa_stats ON e.id_event = pa_stats.id_event
                  LEFT JOIN (
                      SELECT id_event, AVG(score) AS average_rating, COUNT(*) AS rating_count
                      FROM rating
                      WHERE id_event IS NOT NULL
                      GROUP BY id_event
                  ) rating_stats ON e.id_event = rating_stats.id_event
                  ORDER BY $orderBy";

        return $this->dal->executeSelect($query);
    }

    /**
     * Obtem eventos futuros ou passados, mantendo popularidade e rating.
     */
    public function getEventsByTimeStatus($status = 'upcoming') {
        $isPast = $status === 'past';
        $whereSql = $isPast ? "e.event_date < NOW()" : "e.event_date >= NOW()";
        $orderBy = $isPast ? "e.event_date DESC" : "e.event_date ASC";

        $query = "SELECT
                    e.*,
                    COALESCE(pa_stats.popularity_count, 0) AS popularity_count,
                    COALESCE(rating_stats.average_rating, 0) AS average_rating,
                    COALESCE(rating_stats.rating_count, 0) AS rating_count
                  FROM event e
                  LEFT JOIN (
                      SELECT id_event, COUNT(*) AS popularity_count
                      FROM personalagenda
                      GROUP BY id_event
                  ) pa_stats ON e.id_event = pa_stats.id_event
                  LEFT JOIN (
                      SELECT id_event, AVG(score) AS average_rating, COUNT(*) AS rating_count
                      FROM rating
                      WHERE id_event IS NOT NULL
                      GROUP BY id_event
                  ) rating_stats ON e.id_event = rating_stats.id_event
                  WHERE $whereSql
                  ORDER BY $orderBy";

        return $this->dal->executeSelect($query);
    }

    /**
     * Pesquisa e filtra eventos por texto, data, faculdade, tipo e rating minimo.
     * Tambem mantem as metricas de popularidade e rating usadas na interface.
     */
    public function getFilteredEvents($filters = []) {
        $allowedSorts = ['date', 'popularity', 'rating'];
        $sortBy = $filters['sort'] ?? 'date';

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'date';
        }

        $orderBy = "e.event_date ASC";

        if ($sortBy === 'popularity') {
            $orderBy = "popularity_count DESC, e.event_date ASC";
        } elseif ($sortBy === 'rating') {
            $orderBy = "average_rating DESC, rating_count DESC, e.event_date ASC";
        }

        $where = [];
        $params = [];
        $types = "";

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $where[] = "(e.name LIKE ? OR e.description LIKE ? OR e.location LIKE ?)";
            $searchValue = "%" . $search . "%";
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
            $types .= "sss";
        }

        $date = trim($filters['date'] ?? '');
        if ($date !== '') {
            $where[] = "DATE(e.event_date) = ?";
            $params[] = $date;
            $types .= "s";
        }

        $facultyId = intval($filters['faculty_id'] ?? 0);
        if ($facultyId > 0) {
            $where[] = "t.id_faculty = ?";
            $params[] = $facultyId;
            $types .= "i";
        }

        $eventType = trim($filters['event_type'] ?? '');
        if ($eventType !== '') {
            $where[] = "e.event_type = ?";
            $params[] = $eventType;
            $types .= "s";
        }

        $minimumRating = intval($filters['minimum_rating'] ?? 0);
        if ($minimumRating >= 1 && $minimumRating <= 5) {
            $where[] = "COALESCE(rating_stats.average_rating, 0) >= ?";
            $params[] = $minimumRating;
            $types .= "i";
        }

        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        $query = "SELECT 
                    e.*,
                    t.name AS tent_name,
                    f.name AS faculty_name,
                    f.acronym AS faculty_acronym,
                    COALESCE(pa_stats.popularity_count, 0) AS popularity_count,
                    COALESCE(rating_stats.average_rating, 0) AS average_rating,
                    COALESCE(rating_stats.rating_count, 0) AS rating_count
                  FROM event e
                  LEFT JOIN tent t ON e.id_tent = t.id_tent
                  LEFT JOIN faculty f ON t.id_faculty = f.id_faculty
                  LEFT JOIN (
                      SELECT id_event, COUNT(*) AS popularity_count
                      FROM personalagenda
                      GROUP BY id_event
                  ) pa_stats ON e.id_event = pa_stats.id_event
                  LEFT JOIN (
                      SELECT id_event, AVG(score) AS average_rating, COUNT(*) AS rating_count
                      FROM rating
                      WHERE id_event IS NOT NULL
                      GROUP BY id_event
                  ) rating_stats ON e.id_event = rating_stats.id_event
                  $whereSql
                  ORDER BY $orderBy";

        return $this->dal->executeSelect($query, $params, $types);
    }

    /**
     * Devolve os tipos de evento existentes para preencher filtros na interface.
     */
    public function getEventTypes() {
        $query = "SELECT DISTINCT event_type
                  FROM event
                  WHERE event_type IS NOT NULL AND event_type <> ''
                  ORDER BY event_type ASC";

        return $this->dal->executeSelect($query);
    }

    /**
     * Regista um novo utilizador como estudante.
     * Primeiro confirma se o email ja existe. Se existir, devolve false.
     * A password e guardada com hash para nao ficar em texto simples.
     */
    public function registerUser($name, $email, $password) {
        $checkQuery = "SELECT * FROM user WHERE email = ? LIMIT 1";
        $existing = $this->dal->executeSelect($checkQuery, [$email], "s");

        if (!empty($existing)) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO user (name, email, password_hash, id_role)
                  VALUES (?, ?, ?, 2)";

        return $this->dal->executeNonQuery($query, [$name, $email, $passwordHash], "sss") > 0;
    }

    /**
     * Verifica se um email ja esta registado.
     */
    public function isEmailRegistered($email) {
        $query = "SELECT id_user FROM user WHERE email = ?";
        $result = $this->dal->executeSelect($query, [$email], "s");
        return !empty($result);
    }

    /**
     * Autentica um utilizador atraves do email e password.
     * Primeiro tenta validar uma password com hash.
     * Se encontrar uma password antiga em texto simples, valida-a e migra-a
     * automaticamente para hash.
     */
    public function loginUser($email, $password) {
        $email = trim($email);
        $query = "SELECT * FROM user WHERE email = ? LIMIT 1";

        $result = $this->dal->executeSelect($query, [$email], "s");

        if (empty($result)) {
            return false;
        }

        $user = $result[0];
        $storedPassword = (string)($user['password_hash'] ?? '');

        if (password_verify($password, $storedPassword)) {
            return $user;
        }

        // Compatibilidade com bases importadas onde a coluna tem texto simples.
        if ($storedPassword !== '' && hash_equals(trim($storedPassword), $password)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $this->dal->executeNonQuery(
                "UPDATE user SET password_hash = ? WHERE id_user = ?",
                [$newHash, $user['id_user']],
                "si"
            );
            $user['password_hash'] = $newHash;
            return $user;
        }

        return false;
    }

    /**
     * Devolve todas as barracas com os dados da faculdade associada.
     */
    public function getAllTents() {
        $query = "SELECT t.*, f.name AS faculty_name, f.acronym 
                  FROM tent t
                  INNER JOIN faculty f ON t.id_faculty = f.id_faculty
                  ORDER BY t.name ASC";
        return $this->dal->executeSelect($query);
    }

    /**
     * Devolve todos os artistas ordenados pelo nome.
     */
    public function getAllArtists() {
        return $this->dal->executeSelect("SELECT * FROM artist ORDER BY name ASC");
    }

    /**
     * Procura um evento pelo ID e inclui o nome da barraca associada, se existir.
     */
    public function getEventById($id_event) {
        $query = "SELECT e.*, t.name AS tent_name
                  FROM event e
                  LEFT JOIN tent t ON e.id_tent = t.id_tent
                  WHERE e.id_event = ?";
        $result = $this->dal->executeSelect($query, [$id_event], "i");
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Devolve os artistas associados a um evento.
     */
    public function getArtistsByEvent($id_event) {
        $query = "SELECT a.*
                  FROM artist a
                  INNER JOIN event_artist ea ON a.id_artist = ea.id_artist
                  WHERE ea.id_event = ?";
        return $this->dal->executeSelect($query, [$id_event], "i");
    }

    /**
     * Procura uma barraca pelo ID e junta os dados da respetiva faculdade.
     */
    public function getTentById($id_tent) {
        $query = "SELECT 
                        t.*, 
                        f.name AS faculty_name, 
                        f.acronym, 
                        f.description AS faculty_description, 
                        f.color
                  FROM tent t
                  INNER JOIN faculty f ON t.id_faculty = f.id_faculty
                  WHERE t.id_tent = ?";

        $result = $this->dal->executeSelect($query, [$id_tent], "i");

        return !empty($result) ? $result[0] : null;
    }

    /**
     * Procura um artista pelo ID.
     */
    public function getArtistById($id_artist) {
        $query = "SELECT * FROM artist WHERE id_artist = ?";
        $result = $this->dal->executeSelect($query, [$id_artist], "i");
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Calcula o rating medio de um evento.
     */
    public function getAverageEventRating($id_event) {
        $query = "SELECT AVG(score) AS average_rating 
                  FROM rating 
                  WHERE id_event = ?";
        $result = $this->dal->executeSelect($query, [$id_event], "i");
        return $result[0]['average_rating'] ?? null;
    }

    /**
     * Calcula o rating medio de uma barraca.
     */
    public function getAverageTentRating($id_tent) {
        $query = "SELECT AVG(score) AS average_rating 
                  FROM rating 
                  WHERE id_tent = ?";

        $result = $this->dal->executeSelect($query, [$id_tent], "i");

        return $result[0]['average_rating'] ?? null;
    }

    /**
     * Devolve os dados completos de um utilizador, incluindo a role.
     */
    public function getUserById($id_user) {
        $query = "SELECT 
                        u.*, 
                        r.role_name
                  FROM user u
                  INNER JOIN role r ON u.id_role = r.id_role
                  WHERE u.id_user = ?";

        $result = $this->dal->executeSelect($query, [$id_user], "i");

        return !empty($result) ? $result[0] : null;
    }

    /**
     * Atualiza os dados que o utilizador pode editar no proprio perfil.
     */
    public function updateUserProfile($id_user, $name, $email) {
        $query = "UPDATE user 
                  SET name = ?, email = ?
                  WHERE id_user = ?";
        return $this->dal->executeQuery($query, [$name, $email, $id_user], "ssi");
    }

    /**
     * Adiciona um evento a agenda pessoal do utilizador.
     * Se o evento ja existir na agenda, nao duplica o registo.
     */
    public function addEventToAgenda($id_user, $id_event) {
        $check = "SELECT * FROM personalagenda 
                  WHERE id_user = ? AND id_event = ?";
        $exists = $this->dal->executeSelect($check, [$id_user, $id_event], "ii");

        if (!empty($exists)) {
            return false;
        }

        $query = "INSERT INTO personalagenda (id_user, id_event) 
                  VALUES (?, ?)";
        return $this->dal->executeQuery($query, [$id_user, $id_event], "ii");
    }

    /**
     * Devolve todos os eventos guardados na agenda pessoal de um utilizador.
     */
    public function getUserAgenda($id_user) {
        $query = "SELECT pa.*, e.name, e.description, e.event_date, e.location, e.event_type
                  FROM personalagenda pa
                  INNER JOIN event e ON pa.id_event = e.id_event
                  WHERE pa.id_user = ?
                  ORDER BY e.event_date ASC";
        return $this->dal->executeSelect($query, [$id_user], "i");
    }

    /**
     * Remove um evento da agenda pessoal do utilizador.
     */
    public function removeEventFromAgenda($id_user, $id_event) {
        $query = "DELETE FROM personalagenda 
                  WHERE id_user = ? AND id_event = ?";
        return $this->dal->executeQuery($query, [$id_user, $id_event], "ii");
    }

    /**
     * Regista ou atualiza a avaliacao de um utilizador para um evento.
     * Cada utilizador tem apenas uma avaliacao por evento.
     */
    public function rateEvent($id_user, $id_event, $score) {
        $check = "SELECT * FROM rating 
                  WHERE id_user = ? AND id_event = ?";
        $exists = $this->dal->executeSelect($check, [$id_user, $id_event], "ii");

        if (!empty($exists)) {
            $query = "UPDATE rating 
                      SET score = ?
                      WHERE id_user = ? AND id_event = ?";
            return $this->dal->executeQuery($query, [$score, $id_user, $id_event], "iii");
        }

        $query = "INSERT INTO rating (id_user, id_event, score)
                  VALUES (?, ?, ?)";
        return $this->dal->executeQuery($query, [$id_user, $id_event, $score], "iii");
    }

    /**
     * Regista ou atualiza a avaliacao de um utilizador para uma barraca.
     * Cada utilizador tem apenas uma avaliacao por barraca.
     */
    public function rateTent($id_user, $id_tent, $score) {
        $check = "SELECT * FROM rating 
                  WHERE id_user = ? AND id_tent = ?";
        $exists = $this->dal->executeSelect($check, [$id_user, $id_tent], "ii");

        if (!empty($exists)) {
            $query = "UPDATE rating 
                      SET score = ?
                      WHERE id_user = ? AND id_tent = ?";
            return $this->dal->executeQuery($query, [$score, $id_user, $id_tent], "iii");
        }

        $query = "INSERT INTO rating (id_user, id_tent, score)
                  VALUES (?, ?, ?)";
        return $this->dal->executeQuery($query, [$id_user, $id_tent, $score], "iii");
    }

    /**
     * Cria um novo evento. A barraca associada e opcional.
     */
    public function createEvent($name, $description, $event_date, $location, $event_type, $id_tent = null) {
        $query = "INSERT INTO event (name, description, event_date, location, event_type, id_tent)
                  VALUES (?, ?, ?, ?, ?, ?)";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $description, $event_date, $location, $event_type, $id_tent],
            "sssssi"
        ) > 0;
    }

    /**
     * Atualiza os dados principais de um evento existente.
     */
    public function updateEvent($id_event, $name, $description, $event_date, $location, $event_type, $id_tent = null) {
        $query = "UPDATE event
                  SET name = ?, description = ?, event_date = ?, location = ?, event_type = ?, id_tent = ?
                  WHERE id_event = ?";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $description, $event_date, $location, $event_type, $id_tent, $id_event],
            "sssssii"
        ) > 0;
    }

    /**
     * Apaga um evento pelo ID.
     */
    public function deleteEvent($id_event) {
        $query = "DELETE FROM event WHERE id_event = ?";

        return $this->dal->executeNonQuery($query, [$id_event], "i") > 0;
    }

    /**
     * Cria um novo artista.
     */
    public function createArtist($name, $musical_genre, $country, $biography) {
        $query = "INSERT INTO artist (name, musical_genre, country, biography)
                  VALUES (?, ?, ?, ?)";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $musical_genre, $country, $biography],
            "ssss"
        ) > 0;
    }

    /**
     * Apaga um artista pelo ID.
     */
    public function deleteArtist($id_artist) {
        $query = "DELETE FROM artist WHERE id_artist = ?";

        return $this->dal->executeNonQuery($query, [$id_artist], "i") > 0;
    }

    /**
     * Atualiza os dados principais de um artista existente.
     */
    public function updateArtist($id_artist, $name, $musical_genre, $country, $biography) {
        $query = "UPDATE artist
                  SET name = ?, musical_genre = ?, country = ?, biography = ?
                  WHERE id_artist = ?";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $musical_genre, $country, $biography, $id_artist],
            "ssssi"
        ) > 0;
    }

    /**
     * Cria uma nova barraca associada a uma faculdade.
     */
    public function createTent($name, $id_faculty, $location, $open_time, $close_time, $description) {
        $query = "INSERT INTO tent (name, id_faculty, location, open_time, close_time, description)
                  VALUES (?, ?, ?, ?, ?, ?)";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $id_faculty, $location, $open_time, $close_time, $description],
            "sissss"
        ) > 0;
    }

    /**
     * Atualiza os dados principais de uma barraca existente.
     */
    public function updateTent($id_tent, $name, $id_faculty, $location, $open_time, $close_time, $description) {
        $query = "UPDATE tent
                  SET name = ?, id_faculty = ?, location = ?, open_time = ?, close_time = ?, description = ?
                  WHERE id_tent = ?";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $id_faculty, $location, $open_time, $close_time, $description, $id_tent],
            "sissssi"
        ) > 0;
    }

    /**
     * Apaga uma barraca pelo ID.
     */
    public function deleteTent($id_tent) {
        $query = "DELETE FROM tent WHERE id_tent = ?";

        return $this->dal->executeNonQuery($query, [$id_tent], "i") > 0;
    }

    /**
     * Devolve todas as faculdades ordenadas pelo nome.
     */
    public function getAllFaculties() {
        $query = "SELECT * FROM faculty ORDER BY name ASC";

        return $this->dal->executeSelect($query);
    }

    /**
     * Procura uma faculdade pelo ID.
     */
    public function getFacultyById($id_faculty) {
        $query = "SELECT * FROM faculty WHERE id_faculty = ?";

        $result = $this->dal->executeSelect($query, [$id_faculty], "i");

        return !empty($result) ? $result[0] : null;
    }

    /**
     * Cria uma nova faculdade.
     */
    public function createFaculty($name, $acronym, $description, $color = null) {
        $query = "INSERT INTO faculty (name, acronym, description, color)
                  VALUES (?, ?, ?, ?)";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $acronym, $description, $color],
            "ssss"
        ) > 0;
    }

    /**
     * Atualiza os dados de uma faculdade existente.
     */
    public function updateFaculty($id_faculty, $name, $acronym, $description, $color = null) {
        $query = "UPDATE faculty
                  SET name = ?, acronym = ?, description = ?, color = ?
                  WHERE id_faculty = ?";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $acronym, $description, $color, $id_faculty],
            "ssssi"
        ) > 0;
    }

    /**
     * Apaga uma faculdade pelo ID.
     */
    public function deleteFaculty($id_faculty) {
        $query = "DELETE FROM faculty WHERE id_faculty = ?";

        return $this->dal->executeNonQuery($query, [$id_faculty], "i") > 0;
    }

    /**
     * Associa um artista a um evento.
     * Antes de inserir, confirma se a associacao ainda nao existe.
     */
    public function addArtistToEvent($id_event, $id_artist) {
        $check = "SELECT * FROM event_artist
                  WHERE id_event = ? AND id_artist = ?";

        $exists = $this->dal->executeSelect($check, [$id_event, $id_artist], "ii");

        if (!empty($exists)) {
            return false;
        }

        $query = "INSERT INTO event_artist (id_event, id_artist)
                  VALUES (?, ?)";

        return $this->dal->executeNonQuery($query, [$id_event, $id_artist], "ii") > 0;
    }

    /**
     * Remove a associacao entre um artista e um evento.
     */
    public function removeArtistFromEvent($id_event, $id_artist) {
        $query = "DELETE FROM event_artist
                  WHERE id_event = ? AND id_artist = ?";

        return $this->dal->executeNonQuery($query, [$id_event, $id_artist], "ii") > 0;
    }

    /**
     * Lista todas as associacoes entre eventos e artistas com nomes legiveis.
     */
    public function getAllEventArtists() {
        $query = "SELECT 
                        ea.id_event,
                        ea.id_artist,
                        e.name AS event_name,
                        e.event_date,
                        a.name AS artist_name
                  FROM event_artist ea
                  INNER JOIN event e ON ea.id_event = e.id_event
                  INNER JOIN artist a ON ea.id_artist = a.id_artist
                  ORDER BY e.event_date ASC, a.name ASC";

        return $this->dal->executeSelect($query);
    }

    /**
     * Devolve todos os utilizadores com a respetiva role.
     */
    public function getAllUsers() {
        $query = "SELECT 
                        u.id_user, 
                        u.name, 
                        u.email, 
                        u.id_role, 
                        r.role_name
                  FROM user u
                  INNER JOIN role r ON u.id_role = r.id_role
                  ORDER BY u.id_user ASC";

        return $this->dal->executeSelect($query);
    }

    /**
     * Devolve todas as roles disponiveis para atribuir a utilizadores.
     */
    public function getAllRoles() {
        $query = "SELECT 
                        id_role, 
                        role_name AS name
                  FROM role
                  ORDER BY id_role ASC";

        return $this->dal->executeSelect($query);
    }

    /**
     * Cria um utilizador atraves do painel de administracao.
     * Tambem impede emails duplicados e guarda a password com hash.
     */
    public function createUserByAdmin($name, $email, $password, $id_role) {
        $checkQuery = "SELECT * FROM user WHERE email = ? LIMIT 1";
        $existing = $this->dal->executeSelect($checkQuery, [$email], "s");

        if (!empty($existing)) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO user (name, email, password_hash, id_role)
                  VALUES (?, ?, ?, ?)";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $email, $passwordHash, $id_role],
            "sssi"
        ) > 0;
    }

    /**
     * Atualiza nome, email e role de um utilizador atraves do admin.
     */
    public function updateUserByAdmin($id_user, $name, $email, $id_role) {
        $query = "UPDATE user
                  SET name = ?, email = ?, id_role = ?
                  WHERE id_user = ?";

        return $this->dal->executeNonQuery(
            $query,
            [$name, $email, $id_role, $id_user],
            "ssii"
        ) > 0;
    }

    /**
     * Atualiza a password de um utilizador atraves do admin.
     * A nova password e sempre guardada como hash.
     */
    public function updateUserPasswordByAdmin($id_user, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE user
                  SET password_hash = ?
                  WHERE id_user = ?";

        return $this->dal->executeNonQuery(
            $query,
            [$passwordHash, $id_user],
            "si"
        ) > 0;
    }

    /**
     * Apaga um utilizador pelo ID.
     */
    public function deleteUser($id_user) {
        $query = "DELETE FROM user WHERE id_user = ?";

        return $this->dal->executeNonQuery($query, [$id_user], "i") > 0;
    }

    /**
     * Devolve eventos que vao acontecer dentro das proximas horas indicadas.
     * E usado para mostrar alertas de eventos proximos.
     */
    public function getUpcomingAlerts($hours = 48) {
        $query = "SELECT *
                  FROM event
                  WHERE event_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? HOUR)
                  ORDER BY event_date ASC";

        return $this->dal->executeSelect($query, [$hours], "i");
    }

    /**
     * Calcula estatisticas publicas para a pagina inicial.
     * Mostra o evento mais popular pela agenda e o evento melhor cotado pelos ratings.
     */
    public function getPublicEventStats() {
        $popularEvents = $this->getAllEvents('popularity');
        $ratedEvents = $this->getAllEvents('rating');

        $mostPopularEvent = $popularEvents[0] ?? null;
        $highestRatedEvent = null;

        foreach ($ratedEvents as $event) {
            if (!empty($event['rating_count'])) {
                $highestRatedEvent = $event;
                break;
            }
        }

        return [
            'most_popular_event' => $mostPopularEvent,
            'highest_rated_event' => $highestRatedEvent
        ];
    }

    /**
     * Envia uma resposta CSV para o browser.
     */
    public function exportCSV($filename, $headers, $rows) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM para Excel reconhecer UTF-8.
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, $headers, ';');

        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }

        fclose($output);
        exit();
    }

    /**
     * Importa artistas ou eventos a partir de um ficheiro CSV temporario.
     */
    public function importCSV($type, $filePath) {
        if (!in_array($type, ['artists', 'events'], true)) {
            return [
                'success' => false,
                'imported' => 0,
                'message' => 'Tipo de dados invalido.'
            ];
        }

        $handle = fopen($filePath, "r");

        if ($handle === false) {
            return [
                'success' => false,
                'imported' => 0,
                'message' => 'Nao foi possivel abrir o ficheiro CSV.'
            ];
        }

        $rowNumber = 0;
        $imported = 0;

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            $rowNumber++;

            if ($rowNumber == 1) {
                continue;
            }

            if ($type === 'artists' && $this->importArtistCSVRow($data)) {
                $imported++;
            }

            if ($type === 'events' && $this->importEventCSVRow($data)) {
                $imported++;
            }
        }

        fclose($handle);

        return [
            'success' => true,
            'imported' => $imported,
            'message' => 'Importacao concluida. Registos importados: ' . $imported
        ];
    }

    private function importArtistCSVRow($data) {
        $name = trim($data[0] ?? '');
        $musical_genre = trim($data[1] ?? '');
        $country = trim($data[2] ?? '');
        $biography = trim($data[3] ?? '');

        if (empty($name) || empty($musical_genre) || empty($country)) {
            return false;
        }

        return $this->createArtist($name, $musical_genre, $country, $biography);
    }

    private function importEventCSVRow($data) {
        $name = trim($data[0] ?? '');
        $description = trim($data[1] ?? '');
        $event_date = trim($data[2] ?? '');
        $location = trim($data[3] ?? '');
        $event_type = trim($data[4] ?? '');
        $id_tent = !empty($data[5]) ? intval($data[5]) : null;

        if (empty($name) || empty($event_date) || empty($location) || empty($event_type)) {
            return false;
        }

        return $this->createEvent($name, $description, $event_date, $location, $event_type, $id_tent);
    }

    /**
     * Calcula estatisticas simples para o dashboard de administracao:
     * totais, evento mais popular e barraca melhor avaliada.
     */
    public function getAdminStats() {
        // Agrega estatisticas simples para o painel principal sem expor SQL na interface.
        $stats = [];

        $stats['total_events'] = $this->dal->executeSelect("SELECT COUNT(*) AS total FROM event")[0]['total'] ?? 0;
        $stats['total_artists'] = $this->dal->executeSelect("SELECT COUNT(*) AS total FROM artist")[0]['total'] ?? 0;
        $stats['total_tents'] = $this->dal->executeSelect("SELECT COUNT(*) AS total FROM tent")[0]['total'] ?? 0;
        $stats['total_users'] = $this->dal->executeSelect("SELECT COUNT(*) AS total FROM user")[0]['total'] ?? 0;

        $topEvent = $this->dal->executeSelect(
            "SELECT e.name, COUNT(pa.id_user) AS agenda_count
             FROM event e
             LEFT JOIN personalagenda pa ON e.id_event = pa.id_event
             GROUP BY e.id_event, e.name
             ORDER BY agenda_count DESC, e.event_date ASC
             LIMIT 1"
        );
        $stats['most_popular_event'] = $topEvent[0] ?? null;

        $topTent = $this->dal->executeSelect(
            "SELECT t.name, AVG(r.score) AS average_rating
             FROM tent t
             LEFT JOIN rating r ON t.id_tent = r.id_tent
             GROUP BY t.id_tent, t.name
             ORDER BY average_rating DESC
             LIMIT 1"
        );
        $stats['highest_rated_tent'] = $topTent[0] ?? null;

        return $stats;
    }
}
?>
