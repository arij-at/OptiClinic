<?php
// functions.php - Common functions
require_once 'db.php';
require_once 'lang.php';

// Get translation
function t($key) {
    global $lang;
    $current_lang = $_SESSION['lang'] ?? 'fr';
    return $lang[$current_lang][$key] ?? $key;
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Generate random token
function generateToken() {
    return bin2hex(random_bytes(32));
}

// Send email (placeholder, use PHPMailer in production)
function sendEmail($to, $subject, $body) {
    // For now, just log
    error_log("Email to $to: $subject - $body");
    return true;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid or missing CSRF token.');
    }
}
?>
