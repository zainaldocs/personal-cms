<?php
require_once __DIR__ . '/../includes/config.php';

// Unset admin session variables
$_SESSION = array();

// If session cookie is used, destroy it as well
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: " . BASE_URL . "admin/login.php");
exit();
