<?php
require_once 'includes/init.php';
if (!defined('APP_ENV') || APP_ENV !== 'development') { http_response_code(403); exit('Forbidden'); }
// setup.php - Run this to set up the database
require_once 'includes/db.php';

$sql = file_get_contents('database.sql');

// Split into statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $stmt) {
    if (!empty($stmt) && !preg_match('/^--/', $stmt)) {
        try {
            $pdo->exec($stmt);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

echo "Database setup complete.";
?>