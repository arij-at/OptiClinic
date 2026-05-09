<?php
require_once __DIR__ . '/../includes/init.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403); die('Forbidden');
}
require_once '../includes/db.php';

$startDate = new DateTime('tomorrow');
$endDate = clone $startDate;
$endDate->add(new DateInterval('P60D')); // Approximate 60 days

$currentDate = clone $startDate;
$slots = [];

while ($currentDate <= $endDate) {
    // Skip weekends (0 = Sunday, 6 = Saturday)
    if ($currentDate->format('N') < 6) { // 1=Monday, 5=Friday
        for ($hour = 9; $hour < 17; $hour++) {
            $startTime = sprintf('%02d:00:00', $hour);
            $endTime = sprintf('%02d:30:00', $hour);
            $slots[] = [
                'date' => $currentDate->format('Y-m-d'),
                'start_time' => $startTime,
                'end_time' => $endTime
            ];
            $startTime = sprintf('%02d:30:00', $hour);
            $endTime = sprintf('%02d:%02d:00', $hour + 1, 0);
            $slots[] = [
                'date' => $currentDate->format('Y-m-d'),
                'start_time' => $startTime,
                'end_time' => $endTime
            ];
        }
    }
    $currentDate->add(new DateInterval('P1D'));
}

foreach ($slots as $slot) {
    // Check if slot exists
    $stmt = $pdo->prepare("SELECT id FROM time_slots WHERE date = ? AND start_time = ? AND end_time = ?");
    $stmt->execute([$slot['date'], $slot['start_time'], $slot['end_time']]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO time_slots (date, start_time, end_time, is_available) VALUES (?, ?, ?, 1)");
        $stmt->execute([$slot['date'], $slot['start_time'], $slot['end_time']]);
    }
}

echo "Slots seeded successfully.";
?>