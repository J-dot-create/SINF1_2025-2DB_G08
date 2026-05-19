# Guia dos ficheiros PHP

Este documento explica o papel de cada ficheiro PHP do projeto. A ideia e ajudar a apresentar o trabalho, mostrando o que cada pagina faz e como o codigo esta organizado.

O projeto esta dividido em camadas:

- `config`: configuracao da base de dados.
- `src/dal`: acesso direto a base de dados.
- `src/bll`: regras de negocio.
- `public/includes`: cabecalhos, rodapes e componentes reutilizaveis.
- `public/UserInterface`: paginas usadas pelos visitantes/estudantes.
- `public/admin`: paginas usadas pelos administradores.

## config/db_config.php

Guarda as constantes usadas para ligar ao MySQL.

Codigo principal comentado:

```php
define('DB_HOST', 'localhost'); // Servidor da base de dados.
define('DB_USER', 'root');      // Utilizador do MySQL no XAMPP.
define('DB_PASS', '');          // Password do MySQL.
define('DB_NAME', 'sinf1_queima'); // Nome da base de dados do projeto.
```

Este ficheiro e incluido pela DAL, para que os dados de ligacao fiquem centralizados num so local.

## src/dal/DataAccessLayer.php

E a camada de acesso a dados. Faz a ligacao ao MySQL e executa queries preparadas.

Funcoes principais:

- `__construct()`: abre a ligacao a base de dados.
- `executeSelect()`: executa queries `SELECT` e devolve arrays associativos.
- `executeNonQuery()`: executa `INSERT`, `UPDATE` e `DELETE`.
- `executeQuery()`: alias usado em algumas funcoes da BLL para devolver `true` ou `false`.
- `__destruct()`: fecha a ligacao.

Codigo principal comentado:

```php
$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// Cria a ligacao usando as constantes definidas em config/db_config.php.

$stmt = $this->conn->prepare($query);
// Prepara a query para reduzir o risco de SQL injection.

if ($params) {
    $stmt->bind_param($types, ...$params);
}
// Associa os parametros recebidos a query preparada.

$stmt->execute();
// Executa a query.
```

## src/bll/BusinessLogicLayer.php

E a camada de regras de negocio. As paginas nao escrevem SQL diretamente; chamam metodos da BLL, e a BLL usa a DAL.

Responsabilidades principais:

- Listar eventos, artistas, barracas e faculdades.
- Registar e autenticar utilizadores.
- Criar, editar e apagar dados no painel de admin.
- Gerir agenda pessoal.
- Gerir avaliacoes.
- Calcular estatisticas e alertas.

Exemplo comentado: login com migracao de passwords antigas.

```php
if (password_verify($password, $storedPassword)) {
    return $user;
}
// Caso normal: a password introduzida e comparada com o hash guardado.

if ($storedPassword !== '' && hash_equals(trim($storedPassword), $password)) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    // Se a password antiga estava em texto simples, cria um hash seguro.

    $this->dal->executeNonQuery(
        "UPDATE user SET password_hash = ? WHERE id_user = ?",
        [$newHash, $user['id_user']],
        "si"
    );
    // Atualiza a base de dados para deixar de guardar texto simples.

    return $user;
}
```

Exemplo comentado: adicionar evento a agenda.

```php
$check = "SELECT * FROM personalagenda WHERE id_user = ? AND id_event = ?";
$exists = $this->dal->executeSelect($check, [$id_user, $id_event], "ii");
// Primeiro verifica se o evento ja esta na agenda do utilizador.

if (!empty($exists)) {
    return false;
}
// Se ja existir, nao duplica o registo.

$query = "INSERT INTO personalagenda (id_user, id_event) VALUES (?, ?)";
return $this->dal->executeQuery($query, [$id_user, $id_event], "ii");
// Se ainda nao existir, adiciona a agenda.
```

Exemplo comentado: rating de evento.

```php
$check = "SELECT * FROM rating WHERE id_user = ? AND id_event = ?";
$exists = $this->dal->executeSelect($check, [$id_user, $id_event], "ii");
// Verifica se o utilizador ja avaliou este evento.

if (!empty($exists)) {
    $query = "UPDATE rating SET score = ? WHERE id_user = ? AND id_event = ?";
    return $this->dal->executeQuery($query, [$score, $id_user, $id_event], "iii");
}
// Se ja avaliou, atualiza a avaliacao.

$query = "INSERT INTO rating (id_user, id_event, score) VALUES (?, ?, ?)";
return $this->dal->executeQuery($query, [$id_user, $id_event, $score], "iii");
// Se ainda nao avaliou, cria uma nova avaliacao.
```

## public/includes/header_ui.php

Cabecalho usado nas paginas publicas. Inicia a sessao, cria a BLL e mostra a navbar.

O que faz:

- Chama `session_start()` se a sessao ainda nao existir.
- Inclui a `BusinessLogicLayer`.
- Define `$bll`, usado depois pelas paginas publicas.
- Mostra links diferentes conforme o utilizador esteja autenticado.
- Se o utilizador for admin, mostra link para o painel de administracao.

Codigo principal comentado:

```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Garante que existe uma sessao ativa.

$userId = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
$roleId = $_SESSION['role_id'] ?? $_SESSION['id_role'] ?? null;
// Le o utilizador e a role guardados na sessao.

if ($userId) {
    // Mostra opcoes de perfil/logout.
} else {
    // Mostra opcoes de login/registo.
}
```

## public/includes/header_admin.php

Cabecalho usado nas paginas de administracao.

O que faz:

- Inicia sessao.
- Cria a BLL.
- Verifica se o utilizador tem role de administrador.
- Se nao for admin, redireciona para o login.
- Mostra o menu lateral do painel de administracao.

Codigo principal comentado:

```php
$userId = $_SESSION['id_user'] ?? $_SESSION['user_id'] ?? null;
$roleId = $_SESSION['id_role'] ?? $_SESSION['role_id'] ?? null;
// Obtem o utilizador atual e a sua role.

if (!$userId || $roleId != 1) {
    header("Location: ../UserInterface/login.php");
    exit();
}
// Bloqueia o acesso a quem nao estiver autenticado como administrador.
```

## public/includes/footer.php

Fecha a estrutura HTML aberta pelos headers e inclui JavaScript.

Inclui:

- Bootstrap JavaScript via CDN.
- `public/js/main.js`, usado para funcionalidades como confirmar apagamentos ou mostrar passwords.

## public/includes/upcoming_alerts.php

Componente reutilizavel que mostra eventos que vao acontecer em breve.

O que faz:

- Chama `$bll->getUpcomingAlerts()`.
- Se houver eventos nas proximas horas, mostra uma caixa de alerta.
- Se nao houver, nao mostra nada relevante.

## public/index.php

Pagina inicial do site.

O que faz:

- Cria a BLL.
- Obtem todos os eventos.
- Inclui o `header_ui.php`.
- Mostra uma mensagem de boas-vindas.
- Mostra alertas de eventos proximos.
- Lista os eventos em cards.

Fluxo simplificado:

```php
$bll = new BusinessLogicLayer();
$events = $bll->getAllEvents();
// Vai buscar os eventos a BLL.

include 'includes/header_ui.php';
// Carrega a navbar e estrutura base da pagina.

foreach ($events as $event) {
    // Mostra cada evento num card.
}
```

## public/UserInterface/login.php

Pagina de login.

O que faz:

- Inicia sessao.
- Se o utilizador ja estiver autenticado, redireciona.
- Recebe email e password por `POST`.
- Chama `$bll->loginUser()`.
- Guarda dados do utilizador na sessao.
- Redireciona admins para o dashboard e estudantes para a pagina inicial.

Codigo principal comentado:

```php
$user = $bll->loginUser($email, $password);
// Pede a BLL para validar as credenciais.

if ($user) {
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['role_id'] = $user['id_role'];
    // Guarda dados importantes na sessao.

    if ($user['id_role'] == 1) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    // Redireciona conforme a role.
}
```

## public/UserInterface/register.php

Pagina de registo de estudantes.

O que faz:

- Recebe nome, email, password e confirmacao.
- Confirma se as passwords coincidem.
- Chama `$bll->registerUser()`.
- Mostra mensagem de sucesso ou erro.

## public/UserInterface/logout.php

Termina a sessao do utilizador.

Fluxo:

```php
session_start();
session_unset();
session_destroy();
header("Location: login.php");
```

Ou seja, limpa a sessao e volta para o login.

## public/UserInterface/profile.php

Pagina de perfil do utilizador autenticado.

O que faz:

- Obriga login.
- Obtem os dados do utilizador atual.
- Permite alterar nome e email.
- Chama `$bll->updateUserProfile()`.

## public/UserInterface/events.php

Lista todos os eventos.

O que faz:

- Permite ordenar por data, popularidade ou rating.
- Chama `$bll->getAllEvents($sortBy)`.
- Mostra cada evento em cards.
- Inclui popularidade e rating medio.

Codigo principal comentado:

```php
$allowedSorts = ['date', 'popularity', 'rating'];
$sortBy = $_GET['sort'] ?? 'date';
// Recebe a ordenacao escolhida pelo utilizador.

if (!in_array($sortBy, $allowedSorts, true)) {
    $sortBy = 'date';
}
// Evita valores invalidos no parametro da URL.

$events = $bll->getAllEvents($sortBy);
// Vai buscar os eventos ja ordenados.
```

## public/UserInterface/event_detail.php

Mostra detalhes de um evento.

O que faz:

- Le o ID do evento pela URL.
- Busca o evento com `$bll->getEventById()`.
- Busca artistas associados com `$bll->getArtistsByEvent()`.
- Mostra o rating medio.
- Se o utilizador estiver autenticado, permite adicionar a agenda e avaliar.

## public/UserInterface/agenda.php

Pagina da agenda pessoal do utilizador.

O que faz:

- Obriga login.
- Se receber `?add=ID`, adiciona evento a agenda.
- Se receber `?remove=ID`, remove evento da agenda.
- Lista todos os eventos da agenda do utilizador.

## public/UserInterface/rate_event.php

Pagina para avaliar eventos.

O que faz:

- Obriga login.
- Le o ID do evento.
- Valida se o evento existe.
- Recebe uma avaliacao de 1 a 5.
- Chama `$bll->rateEvent()`.

## public/UserInterface/tents.php

Lista todas as barracas.

O que faz:

- Chama `$bll->getAllTents()`.
- Mostra nome, faculdade, localizacao e horarios.
- Linka para a pagina de detalhe de cada barraca.

## public/UserInterface/tent_detail.php

Mostra detalhes de uma barraca.

O que faz:

- Le o ID da barraca pela URL.
- Chama `$bll->getTentById()`.
- Mostra faculdade, localizacao, horario e descricao.
- Mostra rating medio com `$bll->getAverageTentRating()`.
- Se o utilizador estiver autenticado, permite avaliar.

## public/UserInterface/rate_tent.php

Pagina para avaliar barracas.

O que faz:

- Obriga login.
- Le o ID da barraca.
- Valida se a barraca existe.
- Recebe avaliacao de 1 a 5.
- Chama `$bll->rateTent()`.

## public/UserInterface/artists.php

Lista todos os artistas.

O que faz:

- Chama `$bll->getAllArtists()`.
- Mostra nome, genero musical e pais.
- Linka para a pagina de detalhe de cada artista.

## public/UserInterface/artist_detail.php

Mostra detalhes de um artista.

O que faz:

- Le o ID do artista pela URL.
- Chama `$bll->getArtistById()`.
- Mostra dados como nome, genero, pais e biografia.

## public/admin/dashboard.php

Pagina inicial do painel de administracao.

O que faz:

- Usa `header_admin.php`, por isso so admins entram.
- Chama `$bll->getAdminStats()`.
- Mostra totais de eventos, artistas, barracas e utilizadores.
- Mostra evento mais popular e barraca melhor avaliada.

## public/admin/manage_events.php

CRUD de eventos.

O que faz:

- Cria eventos novos.
- Edita eventos existentes.
- Apaga eventos.
- Lista todos os eventos.
- Permite associar um evento a uma barraca.

Codigo principal comentado:

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    // Se o formulario de criacao foi submetido, recolhe os dados.

    if (!empty($name) && !empty($event_date) && !empty($location) && !empty($event_type)) {
        $bll->createEvent($name, $description, $event_date, $location, $event_type, $id_tent);
    }
    // Valida campos obrigatorios e chama a BLL para criar.
}

if (isset($_GET['delete'])) {
    $bll->deleteEvent(intval($_GET['delete']));
}
// Se vier um ID por GET, apaga o evento correspondente.
```

## public/admin/manage_artists.php

CRUD de artistas.

O que faz:

- Cria artistas.
- Edita artistas.
- Apaga artistas.
- Lista artistas existentes.

Usa estes metodos da BLL:

- `createArtist()`
- `updateArtist()`
- `deleteArtist()`
- `getAllArtists()`
- `getArtistById()`

## public/admin/manage_tents.php

CRUD de barracas.

O que faz:

- Cria barracas.
- Edita barracas.
- Apaga barracas.
- Lista barracas existentes.
- Usa faculdades para preencher o campo de associacao.

Usa estes metodos da BLL:

- `createTent()`
- `updateTent()`
- `deleteTent()`
- `getAllTents()`
- `getTentById()`
- `getAllFaculties()`

## public/admin/manage_faculties.php

CRUD de faculdades.

O que faz:

- Cria faculdades.
- Edita faculdades.
- Apaga faculdades.
- Lista faculdades existentes.
- Permite guardar sigla, descricao e cor.

## public/admin/manage_users.php

CRUD de utilizadores.

O que faz:

- Cria utilizadores com role.
- Edita nome, email e role.
- Opcionalmente altera password.
- Apaga utilizadores.
- Impede que o admin apague a propria conta enquanto esta autenticado.

Codigo principal comentado:

```php
if ($currentUserId == $id_user) {
    $message = "Nao podes apagar o teu proprio utilizador...";
}
// Protecao para impedir que o admin remova a propria conta ativa.

if (!empty($new_password)) {
    $bll->updateUserPasswordByAdmin($id_user, $new_password);
}
// A password so e alterada se o campo for preenchido.
```

## public/admin/manage_event_artists.php

Gere a relacao muitos-para-muitos entre eventos e artistas.

O que faz:

- Lista eventos e artistas.
- Permite associar um artista a um evento.
- Permite remover uma associacao.
- Mostra todas as associacoes existentes.

Tabela usada:

- `event_artist`, que liga `id_event` a `id_artist`.

## public/admin/import_csv.php

Importa dados atraves de ficheiros CSV.

O que faz:

- Obriga admin por causa do `header_admin.php`.
- Recebe um ficheiro `.csv`.
- Permite importar artistas ou eventos.
- Le linhas com `fgetcsv()`.
- Ignora a primeira linha, assumindo que e cabecalho.

Codigo principal comentado:

```php
while (($data = fgetcsv($handle, 1000, ";")) !== false) {
    $rowNumber++;

    if ($rowNumber == 1) {
        continue;
    }
    // Ignora a primeira linha do CSV.

    if ($type === 'artists') {
        // Extrai colunas e chama createArtist().
    }

    if ($type === 'events') {
        // Extrai colunas e chama createEvent().
    }
}
```

## public/admin/export_csv.php

Exporta dados da aplicacao para CSV.

O que faz:

- Obriga admin.
- Recebe o tipo a exportar por `GET`, por exemplo `?type=events`.
- Vai buscar dados pela BLL.
- Envia headers para download de CSV.
- Escreve linhas com `fputcsv()`.

Codigo principal comentado:

```php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
// Diz ao browser que a resposta e um ficheiro CSV para descarregar.

$output = fopen('php://output', 'w');
// Escreve diretamente na resposta HTTP.

fputcsv($output, $headers, ';');
// Escreve o cabecalho do CSV.

foreach ($rows as $row) {
    fputcsv($output, $row, ';');
}
// Escreve cada linha de dados.
```

## Resumo para apresentacao

Se tiveres de explicar rapidamente a arquitetura:

1. As paginas em `public` tratam da interface e dos formularios.
2. A `BusinessLogicLayer` contem a logica da aplicacao.
3. A `DataAccessLayer` e a unica camada que comunica diretamente com MySQL.
4. A area admin e protegida por `header_admin.php`.
5. A area publica usa `header_ui.php`.
6. As passwords sao guardadas com hash.
7. As queries usam prepared statements na DAL.
8. A agenda pessoal, ratings e associacoes evento-artista usam tabelas relacionais proprias.

