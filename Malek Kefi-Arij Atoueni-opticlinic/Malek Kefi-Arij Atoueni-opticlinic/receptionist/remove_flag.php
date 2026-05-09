<?php
require_once '../includes/init.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flag_id'])) {
    csrf_verify();
    $flag_id = (int)$_POST['flag_id'];

    try {
        // Mark flag as resolved (manually removed by receptionist)
        $stmt = $pdo->prepare("UPDATE slot_flags SET resolved = 1 WHERE id = ?");
        $stmt->execute([$flag_id]);

        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        error_log('Error removing flag: ' . $e->getMessage());
        header('Location: dashboard.php');
        exit;
    }
}

header('Location: dashboard.php');
?>
