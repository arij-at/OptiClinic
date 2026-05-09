<?php
header('Content-Type: application/json');
require_once 'includes/init.php';
require_once 'includes/db.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['doctor', 'receptionist'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$date = $_GET['date'] ?? '';
if (!$date) {
    http_response_code(400);
    echo json_encode(['error' => 'No date provided']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        a.id, a.patient_id, a.status, a.priority, a.form_id,
        p.name, 
        t.start_time, t.end_time,
        f.symptoms_description, f.q_which_eye, f.q_how_long, f.q_pain_level, f.q_redness, f.q_discharge, f.q_vision, f.triage_score,
        sf.id as flag_id, sf.comment, sf.resolved
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN time_slots t ON a.slot_id = t.id OR a.preferred_slot_id = t.id
    JOIN intake_forms f ON a.form_id = f.id
    LEFT JOIN slot_flags sf ON a.id = sf.appointment_id AND sf.resolved = 0
    WHERE t.date = ? AND a.status = 'confirmed'
    ORDER BY t.start_time
");
$stmt->execute([$date]);
$appts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($appts ?: []);
?>