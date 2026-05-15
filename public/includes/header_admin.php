<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../src/bll/BusinessLogicLayer.php';

$bll = new BusinessLogicLayer();

// Aceita os dois formatos de sessão, caso o teu login use nomes diferentes
$userId = $_SESSION['id_user'] ?? $_SESSION['user_id'] ?? null;
$roleId = $_SESSION['id_role'] ?? $_SESSION['role_id'] ?? null;

// Apenas administradores podem entrar no painel admin
if (!$userId || $roleId != 1) {
    header("Location: ../UserInterface/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração - Queima</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
    <link rel="stylesheet" href="/SINF1_2025-2DB_G08/public/css/style.css">
</head>
<body>
<div class="d-flex">
    <div class="bg-dark text-white p-3 min-vh-100" style="width: 250px;">
        <h3 class="mb-4">Administração</h3>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link text-white">
                    Painel Principal
                </a>
            </li>

            <li class="nav-item">
                <a href="manage_events.php" class="nav-link text-white">
                    Gerir Eventos
                </a>
            </li>

            <li class="nav-item">
                <a href="manage_artists.php" class="nav-link text-white">
                    Gerir Artistas
                </a>
            </li>

            <li class="nav-item">
                <a href="manage_tents.php" class="nav-link text-white">
                    Gerir Barracas
                </a>
            </li>

            <li class="nav-item">
                <a href="manage_faculties.php" class="nav-link text-white">
                    Gerir Faculdades
                </a>
            </li>

            <li class="nav-item">
                <a href="manage_users.php" class="nav-link text-white">
                    Gerir Utilizadores
                </a>
            </li>

            <hr>

            <li class="nav-item">
                <a href="../index.php" class="nav-link text-white">
                    Voltar ao Site
                </a>
            </li>

            <li class="nav-item">
                <a href="../UserInterface/logout.php" class="nav-link text-danger">
                    Terminar Sessão
                </a>
            </li>

            <li class="nav-item">
                <a href="manage_event_artists.php" class="nav-link text-white">
                    Associar Artistas a Eventos
                </a>
            </li>
            <li class="nav-item">
                <a href="export_csv.php" class="nav-link text-white">
                    Exportar CSV
                </a>
            </li>
            <li class="nav-item">
    <a href="import_csv.php" class="nav-link text-white">
        Importar CSV
    </a>
</li>

        </ul>
    </div>

    <main class="p-4 w-100">