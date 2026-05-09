<?php
require_once __DIR__ . '/../../includes/header.php';
$today = date('d M Y');
?>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-section-label">Principal</div>
        <a href="dashboard.php" class="sidebar-btn active">
            <span class="sidebar-icon"><i class="fas fa-calendar-alt"></i></span>
            Agenda
        </a>
        <hr class="sidebar-divider">
        <div class="sidebar-section-label">Compte</div>
        <a href="logout.php" class="sidebar-btn">
            <span class="sidebar-icon"><i class="fas fa-sign-out-alt"></i></span>
            Déconnexion
        </a>
    </aside>

    <!-- Main -->
    <main class="dashboard-main">
        <div class="dashboard-page-header">
            <div>
                <h2><i class="fas fa-user-md me-2" style="color:var(--teal);font-size:1.2rem"></i>Tableau de bord Médecin</h2>
                <p style="margin:0;font-size:.85rem;color:var(--gray-400)">Consultez et gérez vos créneaux de consultation</p>
            </div>
            <div class="date-chip">
                <i class="fas fa-calendar-alt" style="color:var(--teal)"></i>
                <?php echo htmlspecialchars($today, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-calendar-alt" style="color:var(--teal)"></i>
                Rendez-vous d'aujourd'hui
            </div>
            <div class="card-body">
                <?php if (empty($appointments)): ?>
                    <p class="text-muted">Aucun rendez-vous confirmé pour aujourd'hui.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($appointments as $appt): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($appt['name']); ?></h6>
                                        <p class="card-text">
                                            <strong>Heure:</strong> <?php echo htmlspecialchars($appt['start_time'] . ' - ' . $appt['end_time']); ?><br>
                                            <strong>Priorité:</strong> <span class="badge bg-<?php echo $appt['priority'] === 'P1' ? 'danger' : ($appt['priority'] === 'P2' ? 'warning' : 'success'); ?>"><?php echo htmlspecialchars($appt['priority']); ?></span><br>
                                            <strong>Statut:</strong> <?php echo htmlspecialchars($appt['status']); ?>
                                        </p>
                                        <button class="btn btn-sm btn-danger" onclick="flagAppointment(<?php echo htmlspecialchars($appt['id']); ?>)">Signaler un changement</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
function flagAppointment(appointmentId) {
    const comment = prompt('Entrez un commentaire pour le signalement:');
    if (comment !== null && comment.trim() !== '') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'flag.php';
        const inputAppt = document.createElement('input');
        inputAppt.type = 'hidden';
        inputAppt.name = 'appointment_id';
        inputAppt.value = appointmentId;
        form.appendChild(inputAppt);
        const inputComment = document.createElement('input');
        inputComment.type = 'hidden';
        inputComment.name = 'comment';
        inputComment.value = comment;
        form.appendChild(inputComment);
        const inputCsrf = document.createElement('input');
        inputCsrf.type = 'hidden';
        inputCsrf.name = 'csrf_token';
        inputCsrf.value = '<?php echo htmlspecialchars(csrf_token()); ?>';
        form.appendChild(inputCsrf);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
