<?php
// Prevent direct access to functions file
if (basename($_SERVER['PHP_SELF']) == 'functions.php') {
    die('Direct access not permitted');
}

// Require configuration for DB access
require_once __DIR__ . '/config.php';

/**
 * Sanitize output to prevent XSS
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a URL-friendly slug from string
 */
function slugify($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

/**
 * Check if admin is logged in, redirect if not
 */
function check_login() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: " . BASE_URL . "admin/login.php");
        exit();
    }
}

/**
 * Get a configuration setting value from database
 */
function get_setting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

/**
 * Safely upload an image
 * Returns file name on success, throws Exception on failure
 */
function upload_image($file, $target_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No file uploaded or upload error occurred.");
    }

    // Validate size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("File size exceeds 5MB limit.");
    }

    // Validate type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $file['tmp_name']);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.");
    }

    // Create target directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Generate unique name
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid('img_', true) . '.' . $ext;
    $target_path = rtrim($target_dir, '/') . '/' . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $new_filename;
    } else {
        throw new Exception("Failed to move uploaded file.");
    }
}

/**
 * Generate a CSRF token and store it in the session
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die("Invalid CSRF token. Please go back and try again.");
    }
    return true;
}

/**
 * Render a hidden input field with the CSRF token
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . sanitize($token) . '">';
}
