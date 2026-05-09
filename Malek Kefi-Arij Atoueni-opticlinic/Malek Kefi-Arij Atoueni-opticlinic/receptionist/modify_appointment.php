<?php
require_once '../includes/init.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header('Location: login.php');
    exit;
}

// Traitement de la modification de rendez-vous via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['new_slot_id'])) {
    // Vérification du token CSRF
    csrf_verify();
    $appt_id = (int)$_POST['appointment_id'];
    $new_slot_id = (int)$_POST['new_slot_id'];

    try {
        $pdo->beginTransaction();

        // Récupération des informations du rendez-vous actuel
        $stmt = $pdo->prepare("SELECT patient_id, slot_id FROM appointments WHERE id = ?");
        $stmt->execute([$appt_id]);
        $appt = $stmt->fetch();

        if (!$appt) {
            throw new Exception('Appointment not found');
        }

        $old_slot_id = $appt['slot_id'];
        $patient_id = $appt['patient_id'];

        // Mise à jour du rendez-vous avec le nouveau créneau
        $stmt = $pdo->prepare("UPDATE appointments SET slot_id = ?, status = 'modified' WHERE id = ?");
        $stmt->execute([$new_slot_id, $appt_id]);

        // Résolution des signalements non résolus pour ce rendez-vous
        $stmt = $pdo->prepare("UPDATE slot_flags SET resolved = 1 WHERE appointment_id = ? AND resolved = 0");
        $stmt->execute([$appt_id]);

        // Libération de l'ancien créneau
        if ($old_slot_id) {
            $stmt = $pdo->prepare("UPDATE time_slots SET is_available = 1 WHERE id = ?");
            $stmt->execute([$old_slot_id]);
        }

        // Réservation du nouveau créneau
        $stmt = $pdo->prepare("UPDATE time_slots SET is_available = 0 WHERE id = ?");
        $stmt->execute([$new_slot_id]);

        // Insertion de la notification d'excuse
        $stmt = $pdo->prepare("INSERT INTO notifications (patient_id, type, status) VALUES (?, 'apology', 'pending')");
        $stmt->execute([$patient_id]);

        $pdo->commit();

        // Envoi de l'email de notification
        include '../send_notifications.php';

        // Redirection vers le tableau de bord
        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Error modifying appointment: ' . $e->getMessage());
        header('Location: dashboard.php');
        exit;
    }
}

// Redirection si accès direct sans POST
header('Location: dashboard.php');
?>
