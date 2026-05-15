<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../src/bll/BusinessLogicLayer.php';

$bll = new BusinessLogicLayer();

$userId = $_SESSION['id_user'] ?? $_SESSION['user_id'] ?? null;
$roleId = $_SESSION['id_role'] ?? $_SESSION['role_id'] ?? null;

if (!$userId || $roleId != 1) {
    header("Location: ../UserInterface/login.php");
    exit();
}

$type = $_GET['type'] ?? null;

function exportCSV($filename, $headers, $rows) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // BOM para Excel reconhecer UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, $headers, ';');

    foreach ($rows as $row) {
        fputcsv($output, $row, ';');
    }

    fclose($output);
    exit();
}

if ($type === 'events') {
    $events = $bll->getAllEvents();

    $rows = [];
    foreach ($events as $event) {
        $rows[] = [
            $event['id_event'],
            $event['name'],
            $event['description'],
            $event['event_date'],
            $event['location'],
            $event['event_type'],
            $event['id_tent'] ?? ''
        ];
    }

    exportCSV(
        'eventos.csv',
        ['ID', 'Nome', 'Descrição', 'Data', 'Localização', 'Tipo', 'ID Barraca'],
        $rows
    );
}

if ($type === 'artists') {
    $artists = $bll->getAllArtists();

    $rows = [];
    foreach ($artists as $artist) {
        $rows[] = [
            $artist['id_artist'],
            $artist['name'],
            $artist['musical_genre'],
            $artist['country'],
            $artist['biography']
        ];
    }

    exportCSV(
        'artistas.csv',
        ['ID', 'Nome', 'Género Musical', 'País', 'Biografia'],
        $rows
    );
}

if ($type === 'tents') {
    $tents = $bll->getAllTents();

    $rows = [];
    foreach ($tents as $tent) {
        $rows[] = [
            $tent['id_tent'],
            $tent['name'],
            $tent['faculty_name'] ?? '',
            $tent['acronym'] ?? '',
            $tent['location'],
            $tent['open_time'],
            $tent['close_time'],
            $tent['description']
        ];
    }

    exportCSV(
        'barracas.csv',
        ['ID', 'Nome', 'Faculdade', 'Sigla', 'Localização', 'Abertura', 'Fecho', 'Descrição'],
        $rows
    );
}

if ($type === 'faculties') {
    $faculties = $bll->getAllFaculties();

    $rows = [];
    foreach ($faculties as $faculty) {
        $rows[] = [
            $faculty['id_faculty'],
            $faculty['name'],
            $faculty['acronym'],
            $faculty['description'],
            $faculty['color']
        ];
    }

    exportCSV(
        'faculdades.csv',
        ['ID', 'Nome', 'Sigla', 'Descrição', 'Cor'],
        $rows
    );
}

if ($type === 'users') {
    $users = $bll->getAllUsers();

    $rows = [];
    foreach ($users as $user) {
        $rows[] = [
            $user['id_user'],
            $user['name'],
            $user['email'],
            $user['role_name']
        ];
    }

    exportCSV(
        'utilizadores.csv',
        ['ID', 'Nome', 'Email', 'Role'],
        $rows
    );
}

include '../includes/header_admin.php';
?>

<h1 class="display-5 border-bottom pb-2 mb-4">Exportar CSV</h1>

<p class="lead">
    Escolhe a informação que pretendes exportar.
</p>

<div class="row">
    <div class="col-md-4 mb-3">
        <a href="export_csv.php?type=events" class="btn btn-outline-primary w-100">
            Exportar Eventos
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="export_csv.php?type=artists" class="btn btn-outline-primary w-100">
            Exportar Artistas
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="export_csv.php?type=tents" class="btn btn-outline-primary w-100">
            Exportar Barracas
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="export_csv.php?type=faculties" class="btn btn-outline-primary w-100">
            Exportar Faculdades
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="export_csv.php?type=users" class="btn btn-outline-primary w-100">
            Exportar Utilizadores
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>