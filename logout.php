<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

if (is_logged_in()) {
    try {
        $pdo = get_db_connection();
        log_system_action($pdo, $_SESSION['user_id'], "Logout realizado com sucesso");
    } catch (Exception $e) {
        // ignora
    }
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: login.php");
exit;
