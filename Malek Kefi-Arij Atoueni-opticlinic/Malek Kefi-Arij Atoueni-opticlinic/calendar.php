<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$patient_id = $_SESSION['patient_id'] ?? null;
if (!$patient_id) {
    header('Location: index.php');
    exit;
}
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="dashboard-page-header">
                <div>
                    <h2><i class="fas fa-calendar-alt me-2" style="color:var(--teal);font-size:1.2rem"></i>Choisir un créneau</h2>
                    <p style="margin:0;font-size:.85rem;color:var(--gray-400)">Sélectionnez la date et l'heure qui vous conviennent</p>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-check" style="color:var(--teal)"></i>
                    <?php echo $lang_strings['select_slot']; ?>
                </div>
                <div class="card-body">
                    <?php require_once 'includes/shared_calendar.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
