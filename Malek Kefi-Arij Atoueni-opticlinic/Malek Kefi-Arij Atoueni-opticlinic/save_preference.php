<?php
require_once 'includes/init.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Vérification du token CSRF
csrf_verify();

// Récupération de l'ID du patient et du créneau sélectionné
$patient_id = $_SESSION['patient_id'] ?? null;
if (!$patient_id || !isset($_POST['slot_id'])) {
    header('Location: index.php');
    exit;
}

$slot_id = (int)$_POST['slot_id'];

try {
    $pdo->beginTransaction();

    // Récupération des informations du rendez-vous en attente
    $stmt = $pdo->prepare("SELECT a.id as appt_id, a.form_id FROM appointments a WHERE a.patient_id = ? AND a.status = 'pending'");
    $stmt->execute([$patient_id]);
    $appt = $stmt->fetch();

    if (!$appt) {
        header('Location: index.php');
        exit;
    }

    // Vérification de la disponibilité du créneau
    $slotCheck = $pdo->prepare("SELECT is_available FROM time_slots WHERE id = ? FOR UPDATE");
    $slotCheck->execute([$slot_id]);
    $slotRow = $slotCheck->fetch();
    if (!$slotRow || !$slotRow['is_available']) {
        $pdo->rollBack();
        header('Location: calendar.php?error=slot_taken');
        exit;
    }

    // Confirmation complète du rendez-vous
    $stmt = $pdo->prepare("UPDATE appointments SET slot_id = ?, preferred_slot_id = ?, status = 'confirmed' WHERE patient_id = ? AND status = 'pending'");
    $stmt->execute([$slot_id, $slot_id, $patient_id]);

    // Marquage du créneau comme indisponible
    $stmt = $pdo->prepare("UPDATE time_slots SET is_available = 0 WHERE id = ?");
    $stmt->execute([$slot_id]);

    // Insertion de la notification de confirmation
    $stmt = $pdo->prepare("INSERT INTO notifications (patient_id, type, status) VALUES (?, 'confirmation', 'pending')");
    $stmt->execute([$patient_id]);

    $pdo->commit();

    // Envoi de l'email de confirmation
    include 'send_notifications.php';

    // Redirection vers la page d'attente
    header('Location: pending.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Error confirming appointment: ' . $e->getMessage());
    header('Location: index.php');
    exit;
}
?>