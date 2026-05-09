<?php
require_once '../includes/init.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';
require_once '../models/AppointmentModel.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['comment'])) {
    csrf_verify();
    $appt_id = (int)$_POST['appointment_id'];
    $comment = htmlspecialchars(strip_tags(trim($_POST['comment'])));
    $user_id = $_SESSION['user_id'];

    AppointmentModel::flagAppointment($pdo, $appt_id, $user_id, $comment);

    header('Location: dashboard.php');
    exit;
}

header('Location: dashboard.php');
?>