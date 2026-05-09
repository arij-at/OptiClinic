<?php
require_once '../includes/init.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';
require_once '../models/FormModel.php';
require_once '../models/SlotModel.php';
require_once '../models/AppointmentModel.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header('Location: login.php');
    exit;
}

// Traitement de l'assignation de créneau via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_id'], $_POST['slot_id'])) {
    // Vérification du token CSRF
    csrf_verify();
    $form_id = (int)$_POST['form_id'];
    $slot_id = (int)$_POST['slot_id'];
    try {
        $pdo->beginTransaction();
        // Libération du créneau préféré existant si différent
        $existing_preferred = FormModel::getPreferredSlotIdByFormId($pdo, $form_id);
        if ($existing_preferred && $existing_preferred != $slot_id) {
            SlotModel::releaseSlot($pdo, $existing_preferred);
        }
        // Assignation du nouveau créneau
        AppointmentModel::assignSlot($pdo, $slot_id, $form_id);
        SlotModel::reserveSlot($pdo, $slot_id);
        $patient_id = FormModel::getPatientIdByFormId($pdo, $form_id);
        AppointmentModel::createConfirmationNotification($pdo, $patient_id);
        $pdo->commit();
        // Envoi des notifications
        include '../send_notifications.php';
        unset($_SESSION['assigning_form_id']);
        // Redirection vers le tableau de bord
        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Erreur lors de l\'assignation : ' . $e->getMessage();
    }
}

// Affichage du formulaire d'assignation via GET
$form_id = $_GET['form_id'] ?? null;
if ($form_id) {
    // Récupération des données du formulaire et des créneaux disponibles
    $form  = FormModel::getFormWithPatientName($pdo, $form_id);
    $slots = SlotModel::getAvailableSlots($pdo, 20);
    require_once '../includes/header.php';
    ?>
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="dashboard-page-header">
                    <div>
                        <h2><i class="fas fa-calendar-plus me-2" style="color:var(--teal);font-size:1.2rem"></i>Assigner un créneau</h2>
                        <p style="margin:0;font-size:.85rem;color:var(--gray-400)">Patient : <strong><?php echo htmlspecialchars($form['name']); ?></strong></p>
                    </div>
                    <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Retour</a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-alt" style="color:var(--teal)"></i>
                        Sélectionner un créneau disponible
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($form_id); ?>">
                            <div class="mb-3">
                                <label class="form-label">Créneaux disponibles</label>
                                <select name="slot_id" class="form-select" required>
                                    <option value="">— Choisir un créneau —</option>
                                    <?php foreach ($slots as $slot): ?>
                                        <option value="<?php echo htmlspecialchars($slot['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($slot['date'] . ' · ' . $slot['start_time'] . ' – ' . $slot['end_time']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check me-2"></i>Confirmer l'assignation
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    require_once '../includes/footer.php';
    exit;
}

header('Location: dashboard.php');
?>
