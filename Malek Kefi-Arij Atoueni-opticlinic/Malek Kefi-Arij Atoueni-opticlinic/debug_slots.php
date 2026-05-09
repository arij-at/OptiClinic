<?php
require_once 'includes/init.php';
if (!defined('APP_ENV') || APP_ENV !== 'development') { http_response_code(403); exit('Forbidden'); }
require_once 'includes/db.php';

// Check time slots
$stmt = $pdo->query("SELECT COUNT(*) as total FROM time_slots");
$total = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as available FROM time_slots WHERE is_available = 1");
$available = $stmt->fetch()['available'];

echo "Total slots: $total\n";
echo "Available slots: $available\n";

// Check future dates with available slots
$stmt = $pdo->prepare("SELECT DISTINCT date FROM time_slots WHERE date >= CURDATE() AND is_available = 1 ORDER BY date LIMIT 5");
$stmt->execute();
$dates = $stmt->fetchAll();

echo "\nNext 5 dates with available slots:\n";
foreach ($dates as $row) {
    echo "  - " . $row['date'] . "\n";
}

// Check sessions
echo "\n\nSession data:\n";
session_start();
echo "patient_id: " . ($_SESSION['patient_id'] ?? 'NOT SET') . "\n";
echo "role: " . ($_SESSION['role'] ?? 'NOT SET (defaults to patient)') . "\n";
echo "token: " . ($_SESSION['token'] ?? 'NOT SET') . "\n";
?>
