<?php
require_once __DIR__ . '/../dal/DataAccessLayer.php';

/**
 * Business Logic Layer (BLL)
 * Contém todas as regras de negócio e atua como intermediário entre a UI e a DAL.
 */
class BusinessLogicLayer {
    private $dal;

    public function __construct() {
        $this->dal = new DataAccessLayer();
    }

    /**
     * Exemplo: Obter todos os eventos da base de dados
     */
    public function getAllEvents() {
        return $this->dal->executeSelect("SELECT * FROM event ORDER BY event_date ASC");
    }

    /**
     * Regista um novo utilizador na base de dados como 'Student' (id_role = 2)
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
     * Verifica se um email já existe na base de dados
     */
    public function isEmailRegistered($email) {
        $query = "SELECT id_user FROM user WHERE email = ?";
        $result = $this->dal->executeSelect($query, [$email], "s");
        return !empty($result);
    }

    /**
     * Autentica um utilizador através do email e password
     * Devolve os dados do utilizador se tiver sucesso, ou null caso contrário.
     */
    public function loginUser($email, $password) {
    $query = "SELECT * FROM user WHERE email = ? LIMIT 1";

    $result = $this->dal->executeSelect($query, [$email], "s");

    if (empty($result)) {
        return false;
    }

    $user = $result[0];

    if (password_verify($password, $user['password_hash'])) {
        return $user;
    }

    // Compatibilidade com dados antigos importados antes de se usar password_hash().
    if (hash_equals($user['password_hash'], $password)) {
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


public function getAllTents() {
    $query = "SELECT t.*, f.name AS faculty_name, f.acronym 
              FROM tent t
              INNER JOIN faculty f ON t.id_faculty = f.id_faculty
              ORDER BY t.name ASC";
    return $this->dal->executeSelect($query);
}

public function getAllArtists() {
    return $this->dal->executeSelect("SELECT * FROM artist ORDER BY name ASC");
}

public function getEventById($id_event) {
    $query = "SELECT e.*, t.name AS tent_name
              FROM event e
              LEFT JOIN tent t ON e.id_tent = t.id_tent
              WHERE e.id_event = ?";
    $result = $this->dal->executeSelect($query, [$id_event], "i");
    return !empty($result) ? $result[0] : null;
}

public function getArtistsByEvent($id_event) {
    $query = "SELECT a.*
              FROM artist a
              INNER JOIN event_artist ea ON a.id_artist = ea.id_artist
              WHERE ea.id_event = ?";
    return $this->dal->executeSelect($query, [$id_event], "i");
}

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

public function getArtistById($id_artist) {
    $query = "SELECT * FROM artist WHERE id_artist = ?";
    $result = $this->dal->executeSelect($query, [$id_artist], "i");
    return !empty($result) ? $result[0] : null;
}

public function getAverageEventRating($id_event) {
    $query = "SELECT AVG(score) AS average_rating 
              FROM rating 
              WHERE id_event = ?";
    $result = $this->dal->executeSelect($query, [$id_event], "i");
    return $result[0]['average_rating'] ?? null;
}

public function getAverageTentRating($id_tent) {
    $query = "SELECT AVG(score) AS average_rating 
              FROM rating 
              WHERE id_tent = ?";

    $result = $this->dal->executeSelect($query, [$id_tent], "i");

    return $result[0]['average_rating'] ?? null;
}




// Logica do loging de usuarios`


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

public function updateUserProfile($id_user, $name, $email) {
    $query = "UPDATE user 
              SET name = ?, email = ?
              WHERE id_user = ?";
    return $this->dal->executeQuery($query, [$name, $email, $id_user], "ssi");
}

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

public function getUserAgenda($id_user) {
    $query = "SELECT pa.*, e.name, e.description, e.event_date, e.location, e.event_type
              FROM personalagenda pa
              INNER JOIN event e ON pa.id_event = e.id_event
              WHERE pa.id_user = ?
              ORDER BY e.event_date ASC";
    return $this->dal->executeSelect($query, [$id_user], "i");
}

public function removeEventFromAgenda($id_user, $id_event) {
    $query = "DELETE FROM personalagenda 
              WHERE id_user = ? AND id_event = ?";
    return $this->dal->executeQuery($query, [$id_user, $id_event], "ii");
}

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




// criar eventos e deletar eventos como admin
public function createEvent($name, $description, $event_date, $location, $event_type, $id_tent = null) {
    $query = "INSERT INTO event (name, description, event_date, location, event_type, id_tent)
              VALUES (?, ?, ?, ?, ?, ?)";

    return $this->dal->executeNonQuery(
        $query,
        [$name, $description, $event_date, $location, $event_type, $id_tent],
        "sssssi"
    ) > 0;
}

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

public function deleteEvent($id_event) {
    $query = "DELETE FROM event WHERE id_event = ?";

    return $this->dal->executeNonQuery($query, [$id_event], "i") > 0;
}
    

// Codigo para o gerenciamento dos artistas


public function createArtist($name, $musical_genre, $country, $biography) {
    $query = "INSERT INTO artist (name, musical_genre, country, biography)
              VALUES (?, ?, ?, ?)";

    return $this->dal->executeNonQuery(
        $query,
        [$name, $musical_genre, $country, $biography],
        "ssss"
    ) > 0;
}

public function deleteArtist($id_artist) {
    $query = "DELETE FROM artist WHERE id_artist = ?";

    return $this->dal->executeNonQuery($query, [$id_artist], "i") > 0;
}

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


public function createTent($name, $id_faculty, $location, $open_time, $close_time, $description) {
    $query = "INSERT INTO tent (name, id_faculty, location, open_time, close_time, description)
              VALUES (?, ?, ?, ?, ?, ?)";

    return $this->dal->executeNonQuery(
        $query,
        [$name, $id_faculty, $location, $open_time, $close_time, $description],
        "sissss"
    ) > 0;
}

// Gerir barracas como admin

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

public function deleteTent($id_tent) {
    $query = "DELETE FROM tent WHERE id_tent = ?";

    return $this->dal->executeNonQuery($query, [$id_tent], "i") > 0;
}


// CRUD para as faculdades
public function getAllFaculties() {
    $query = "SELECT * FROM faculty ORDER BY name ASC";

    return $this->dal->executeSelect($query);
}

public function getFacultyById($id_faculty) {
    $query = "SELECT * FROM faculty WHERE id_faculty = ?";

    $result = $this->dal->executeSelect($query, [$id_faculty], "i");

    return !empty($result) ? $result[0] : null;
}

public function createFaculty($name, $acronym, $description, $color = null) {
    $query = "INSERT INTO faculty (name, acronym, description, color)
              VALUES (?, ?, ?, ?)";

    return $this->dal->executeNonQuery(
        $query,
        [$name, $acronym, $description, $color],
        "ssss"
    ) > 0;
}

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

public function deleteFaculty($id_faculty) {
    $query = "DELETE FROM faculty WHERE id_faculty = ?";

    return $this->dal->executeNonQuery($query, [$id_faculty], "i") > 0;
}


//Gerir eventos e artistas associados a uma barraca

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

public function removeArtistFromEvent($id_event, $id_artist) {
    $query = "DELETE FROM event_artist
              WHERE id_event = ? AND id_artist = ?";

    return $this->dal->executeNonQuery($query, [$id_event, $id_artist], "ii") > 0;
}

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


// Gerir utilizadores como admin

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

public function getAllRoles() {
    $query = "SELECT 
                    id_role, 
                    role_name AS name
              FROM role
              ORDER BY id_role ASC";

    return $this->dal->executeSelect($query);
}

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

public function deleteUser($id_user) {
    $query = "DELETE FROM user WHERE id_user = ?";

    return $this->dal->executeNonQuery($query, [$id_user], "i") > 0;
}



// Alertas de eventos a seguir


public function getUpcomingAlerts($hours = 48) {
    $query = "SELECT *
              FROM event
              WHERE event_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? HOUR)
              ORDER BY event_date ASC";

    return $this->dal->executeSelect($query, [$hours], "i");
}

public function getAdminStats() {
    // Agrega estatisticas simples para o painel principal sem expor logica SQL na interface.
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



// Pesquisa e Filtros de Eventos



}
?>
