<?php
// Prevent direct access to config file
if (basename($_SERVER['PHP_SELF']) == 'config.php') {
    die('Direct access not permitted');
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'personal_cms');

try {
    // Create PDO connection with options for safety and error handling
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // In production, log error and show a user-friendly message
    die("Database connection failed: " . $e->getMessage());
}

// Define Base URL dynamically and correctly regardless of current script directory
$physicalRoot = str_replace('\\', '/', dirname(__DIR__));
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$baseUri = str_replace($docRoot, '', $physicalRoot);
$baseUri = '/' . ltrim($baseUri, '/'); // ensure leading slash

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . $host . rtrim($baseUri, '/') . '/';
define('BASE_URL', $baseUrl);
