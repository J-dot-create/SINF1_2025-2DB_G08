<?php
// Base header for all pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../src/bll/BusinessLogicLayer.php';
$bll = new BusinessLogicLayer();

// Aceita os dois formatos de sessão
$userId = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
$roleId = $_SESSION['role_id'] ?? $_SESSION['id_role'] ?? null;
$userName = $_SESSION['user_name'] ?? 'Utilizador';

// Base URL do projeto
$baseUrl = '/SINF1_2025-2DB_G08/public';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queima das Fitas Porto</title>

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
    <link rel="stylesheet" href="/SINF1_2025-2DB_G08/public/css/style.css">

    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css">
</head>

<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $baseUrl; ?>/index.php">
            Queima das Fitas
        </a>

        <button 
            class="navbar-toggler" 
            type="button" 
            data-bs-toggle="collapse" 
            data-bs-target="#navbarContent"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/index.php">
                        Página Inicial
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/UserInterface/events.php">
                        Eventos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/UserInterface/tents.php">
                        Barracas
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/UserInterface/artists.php">
                        Artistas
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($userId): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo $baseUrl; ?>/UserInterface/profile.php">
                            Olá, <?php echo htmlspecialchars($userName); ?>
                        </a>
                    </li>

                    <?php if ($roleId == 1): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="<?php echo $baseUrl; ?>/admin/dashboard.php">
                                Painel de Administração
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-info" href="<?php echo $baseUrl; ?>/UserInterface/agenda.php">
                                A Minha Agenda
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?php echo $baseUrl; ?>/UserInterface/logout.php">
                            Terminar Sessão
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>/UserInterface/login.php">
                            Iniciar Sessão
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>/UserInterface/register.php">
                            Registar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">