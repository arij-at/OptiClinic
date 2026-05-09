<?php
require_once 'includes/init.php';
require_once 'includes/db.php';
require_once 'models/SlotModel.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['patient_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$date = $_GET['date'] ?? '';
if (!$date) {
    echo json_encode([]);
    exit;
}

if ($date === 'all') {
    $slots = SlotModel::getFutureSlots($pdo);
} else {
    $slots = SlotModel::getSlotsByDate($pdo, $date);
}


header('Content-Type: application/json');
echo json_encode($slots);
?>