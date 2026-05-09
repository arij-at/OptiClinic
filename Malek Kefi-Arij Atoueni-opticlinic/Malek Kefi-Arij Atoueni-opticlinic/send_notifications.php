<?php
require_once 'includes/db.php';

$baseUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/');

// Get pending notifications
$stmt = $pdo->query("SELECT n.*, p.email, p.name, p.token, a.id as appt_id, t.date, t.start_time, t.end_time FROM notifications n JOIN patients p ON n.patient_id = p.id LEFT JOIN appointments a ON p.id = a.patient_id LEFT JOIN time_slots t ON a.slot_id = t.id WHERE n.status = 'pending'");
$notifications = $stmt->fetchAll();

foreach ($notifications as $notif) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: OptiClinic <noreply@opticlinic.com>' . "\r\n";

    if ($notif['type'] === 'confirmation') {
        if (!$notif['date']) continue; // Skip if no slot assigned
        $subject = 'Confirmation de votre rendez-vous — OptiClinic';
        $body = "
            <html>
            <head><title>Confirmation de votre rendez-vous</title></head>
            <body>
                <h2>Confirmation de votre rendez-vous</h2>
                <p>Cher {$notif['name']},</p>
                <p>Votre rendez-vous a été confirmé pour le {$notif['date']} à {$notif['start_time']} - {$notif['end_time']}.</p>
                <p>Vous pouvez consulter les détails sur <a href='{$baseUrl}/appointment.php?token={$notif['token']}'>cette page</a>.</p>
                <p>Cordialement,<br>OptiClinic</p>
            </body>
            </html>
        ";
    } elseif ($notif['type'] === 'apology') {
        if (!$notif['date']) continue; // Skip if no slot
        $subject = 'Modification de votre rendez-vous — OptiClinic';
        $body = "
            <html>
            <head><title>Modification de votre rendez-vous</title></head>
            <body>
                <h2>Modification de votre rendez-vous</h2>
                <p>Cher {$notif['name']},</p>
                <p>Nous nous excusons pour la modification de votre rendez-vous. Le nouveau créneau est le {$notif['date']} à {$notif['start_time']} - {$notif['end_time']}.</p>
                <p>Vous pouvez consulter les détails sur <a href='{$baseUrl}/appointment.php?token={$notif['token']}'>cette page</a>.</p>
                <p>Cordialement,<br>OptiClinic</p>
            </body>
            </html>
        ";
    }

    if (mail($notif['email'], $subject, $body, $headers)) {
        $stmt = $pdo->prepare("UPDATE notifications SET status = 'sent', sent_at = NOW() WHERE id = ?");
        $stmt->execute([$notif['id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE notifications SET status = 'failed' WHERE id = ?");
        $stmt->execute([$notif['id']]);
    }
}
?>