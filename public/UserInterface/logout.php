<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpar todas as variáveis da sessão
$_SESSION = [];

// Apagar o cookie da sessão, se existir
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir a sessão
session_destroy();

// Redirecionar para a página inicial
header("Location: ../index.php");
exit;