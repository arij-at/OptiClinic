<?php
require_once 'includes/init.php';
if (!defined('APP_ENV') || APP_ENV !== 'development') { http_response_code(403); exit('Forbidden'); }
session_start();
echo '<h3>Session Debug</h3>';
echo '<pre>';
echo 'patient_id: ' . ($_SESSION['patient_id'] ?? 'NOT SET') . "\n";
echo 'role: ' . ($_SESSION['role'] ?? 'NOT SET') . "\n";
echo 'user_id: ' . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo 'token: ' . ($_SESSION['token'] ?? 'NOT SET') . "\n";
echo '</pre>';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'receptionist') {
    echo '<p style="color: red;"><strong>WARNING: You are logged in as receptionist!</strong></p>';
    echo '<a href="receptionist/logout.php">Logout as receptionist</a>';
} elseif (isset($_SESSION['patient_id'])) {
    echo '<p style="color: green;"><strong>You are a patient with ID: ' . $_SESSION['patient_id'] . '</strong></p>';
} else {
    echo '<p>You have no active session.</p>';
}
?>