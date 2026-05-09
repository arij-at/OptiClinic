<?php
if (!defined('APP_ENV') || APP_ENV !== 'development') { http_response_code(403); exit('Forbidden'); }
require_once 'includes/header.php';
require_once 'includes/db.php';

// Check current session
$user_role = $_SESSION['role'] ?? 'patient';
$patient_id = $_SESSION['patient_id'] ?? null;
$is_assigning = ($_SESSION['role'] ?? null) === 'receptionist' && ($_SESSION['assigning_form_id'] ?? null);

echo '<div class="container mt-4 mb-5">';
echo '<div class="row justify-content-center"><div class="col-lg-8">';
echo '<div class="card">';
echo '<div class="card-header"><h4>🔍 Diagnostic Report</h4></div>';
echo '<div class="card-body">';

echo '<h5>Session Info:</h5>';
echo '<ul>';
echo '<li>patient_id: <strong>' . ($patient_id ?? 'NOT SET') . '</strong></li>';
echo '<li>role: <strong>' . ($user_role ?? 'NOT SET') . '</strong></li>';
echo '<li>is_assigning: <strong>' . ($is_assigning ? 'YES' : 'NO') . '</strong></li>';
echo '</ul>';

echo '<h5>Calendar Logic:</h5>';
echo '<ul>';
echo '<li>user_role === "patient": <strong>' . ($user_role === 'patient' ? 'YES' : 'NO') . '</strong></li>';
echo '<li>Will show slotsModal: <strong>' . (($user_role === 'patient' || $is_assigning) ? 'YES ✓' : 'NO ✗') . '</strong></li>';
echo '</ul>';

echo '<h5>Database Info:</h5>';
$stmt = $pdo->query('SELECT COUNT(*) as cnt FROM time_slots');
$row = $stmt->fetch();
echo '<ul><li>Total time_slots: <strong>' . $row['cnt'] . '</strong></li>';

$stmt = $pdo->query('SELECT COUNT(*) as cnt FROM time_slots WHERE is_available=1');
$row = $stmt->fetch();
echo '<li>Available slots (is_available=1): <strong>' . $row['cnt'] . '</strong></li>';

$stmt = $pdo->prepare('SELECT COUNT(*) as cnt FROM time_slots WHERE date = ? AND is_available=1');
$stmt->execute([date('Y-m-d')]);
$row = $stmt->fetch();
echo '<li>Available for today (' . date('Y-m-d') . '): <strong>' . $row['cnt'] . '</strong></li>';
echo '</ul>';

// Test hasAvailableSlots for today
$todayHasSlots = false;
$stmt = $pdo->prepare("SELECT COUNT(*) FROM time_slots WHERE date = ? AND is_available = 1");
$stmt->execute([date('Y-m-d')]);
$todayHasSlots = $stmt->fetchColumn() > 0;

echo '<h5>Expected Calendar Display:</h5>';
echo '<ul>';
if ($user_role === 'patient' || $is_assigning) {
    echo '<li>Modal to show: <strong>slotsModal (Patient slots)</strong> ✓</li>';
    echo '<li>Today cell class: <strong>' . ($todayHasSlots ? 'bg-success text-white available-day' : 'bg-secondary text-white') . '</strong></li>';
    echo '<li>Today will be clickable: <strong>' . ($todayHasSlots ? 'YES ✓' : 'NO') . '</strong></li>';
} else {
    echo '<li>Modal to show: <strong>appointmentsModal (Staff view)</strong> ✗</li>';
    echo '<li>ERROR: Patient seeing staff calendar!</li>';
}
echo '</ul>';

echo '<hr>';
echo '<p class="text-muted text-sm">If you see "Patient seeing staff calendar!" above, the session role is incorrect.</p>';
echo '</div></div></div></div></div>';

require_once 'includes/footer.php';
?>
