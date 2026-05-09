<?php
require_once '../includes/init.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flag_id'], $_POST['new_slot_id'])) {
    csrf_verify();
    $flag_id = (int)$_POST['flag_id'];
    $new_slot_id = (int)$_POST['new_slot_id'];

    try {
        $pdo->beginTransaction();

        // Get appointment from flag
        $stmt = $pdo->prepare("SELECT appointment_id FROM slot_flags WHERE id = ?");
        $stmt->execute([$flag_id]);
        $appt_id = $stmt->fetchColumn();

        // Get old slot
        $stmt = $pdo->prepare("SELECT slot_id, patient_id FROM appointments WHERE id = ?");
        $stmt->execute([$appt_id]);
        $appt = $stmt->fetch();

        // Update appointment
        $stmt = $pdo->prepare("UPDATE appointments SET slot_id = ?, status = 'modified' WHERE id = ?");
        $stmt->execute([$new_slot_id, $appt_id]);

        // Mark old slot available
        $stmt = $pdo->prepare("UPDATE time_slots SET is_available = 1 WHERE id = ?");
        $stmt->execute([$appt['slot_id']]);

        // Mark new slot unavailable
        $stmt = $pdo->prepare("UPDATE time_slots SET is_available = 0 WHERE id = ?");
        $stmt->execute([$new_slot_id]);

        // Insert apology notification
        $stmt = $pdo->prepare("INSERT INTO notifications (patient_id, type, status) VALUES (?, 'apology', 'pending')");
        $stmt->execute([$appt['patient_id']]);

        // Resolve flag
        $stmt = $pdo->prepare("UPDATE slot_flags SET resolved = 1 WHERE id = ?");
        $stmt->execute([$flag_id]);

        $pdo->commit();

        // Call send_notifications.php
        include '../send_notifications.php';

        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Error modifying slot: ' . $e->getMessage();
    }
}

// If GET flag_id, show slot selection
$flag_id = $_GET['flag_id'] ?? null;
if ($flag_id) {
    $stmt = $pdo->prepare("
        SELECT sf.*, a.id as appt_id, p.name, t.date, t.start_time, t.end_time
        FROM slot_flags sf
        JOIN appointments a ON sf.appointment_id = a.id
        JOIN patients p ON a.patient_id = p.id
        LEFT JOIN time_slots t ON a.slot_id = t.id
        WHERE sf.id = ?
    ");
    $stmt->execute([$flag_id]);
    $flag = $stmt->fetch();

    // Get available slots
    $stmt = $pdo->query("SELECT * FROM time_slots WHERE is_available = 1 ORDER BY date, start_time LIMIT 20");
    $slots = $stmt->fetchAll();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Modify Slot</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5">
        <h3>Modify Slot for <?php echo htmlspecialchars($flag['name']); ?> (Current: <?php echo htmlspecialchars($flag['date'] . ' ' . $flag['start_time']); ?>)</h3>
        <p>Comment: <?php echo htmlspecialchars($flag['comment']); ?></p>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="flag_id" value="<?php echo $flag_id; ?>">
            <select name="new_slot_id" class="form-select" required>
                <option value="">Select New Slot</option>
                <?php foreach ($slots as $slot): ?>
                    <option value="<?php echo $slot['id']; ?>"><?php echo $slot['date'] . ' ' . $slot['start_time'] . ' - ' . $slot['end_time']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-2">Confirm Modification</button>
        </form>
        <?php if (isset($error)) echo '<div class="alert alert-danger mt-2">' . $error . '</div>'; ?>
    </div>
    </body>
    </html>
    <?php
    exit;
}

header('Location: dashboard.php');
?>