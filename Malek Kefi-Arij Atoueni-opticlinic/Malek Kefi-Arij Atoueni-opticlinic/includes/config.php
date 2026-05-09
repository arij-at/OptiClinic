<?php
// config.php - Database configuration
$dotenvPath = __DIR__ . '/../.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        [$name, $value] = array_map('trim', explode('=', $line, 2) + ['', '']);
        if ($name !== '' && !array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3307');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'opticlinic');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');