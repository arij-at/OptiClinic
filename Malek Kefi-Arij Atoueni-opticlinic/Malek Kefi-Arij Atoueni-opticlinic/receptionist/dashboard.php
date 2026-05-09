<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../controllers/ReceptionistController.php';
ReceptionistController::dashboard($pdo);
?>

