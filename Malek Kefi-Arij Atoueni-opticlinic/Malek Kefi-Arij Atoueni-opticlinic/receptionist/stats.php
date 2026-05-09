<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../models/AppointmentModel.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header('Location: login.php');
    exit;
}

$weeklyStatusCounts = AppointmentModel::getWeeklyAppointmentStatusCounts($pdo);
$assignmentCounts   = AppointmentModel::getAssignmentCountByRouting($pdo);
$priorityCounts     = AppointmentModel::getPriorityCounts($pdo);
$unresolvedFlags    = AppointmentModel::getUnresolvedSlotFlagDetails($pdo);

$summaryByStatus = ['pending' => 0, 'confirmed' => 0, 'modified' => 0];
foreach ($weeklyStatusCounts as $row) {
    if (isset($summaryByStatus[$row['status']])) {
        $summaryByStatus[$row['status']] = (int)$row['count'];
    }
}

$assignmentMap = ['auto' => 0, 'receptionist' => 0];
foreach ($assignmentCounts as $row) {
    if (isset($assignmentMap[$row['routing']])) {
        $assignmentMap[$row['routing']] = (int)$row['count'];
    }
}

$priorityMap = ['P1' => 0, 'P2' => 0, 'P3' => 0];
foreach ($priorityCounts as $row) {
    if (isset($priorityMap[$row['priority']])) {
        $priorityMap[$row['priority']] = (int)$row['count'];
    }
}

$totalWeekly = max(1, array_sum($summaryByStatus));
$totalAssign = max(1, array_sum($assignmentMap));
$totalPriority = max(1, array_sum($priorityMap));

require_once __DIR__ . '/../includes/header.php';
$today = date('d M Y');
?>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-section-label">Navigation</div>
        <a href="dashboard.php" class="sidebar-btn">
            <span class="sidebar-icon"><i class="fas fa-th-large"></i></span>
            Tableau de bord
        </a>
        <a href="stats.php" class="sidebar-btn active">
            <span class="sidebar-icon"><i class="fas fa-chart-bar"></i></span>
            Statistiques
        </a>
        <hr class="sidebar-divider">
        <a href="logout.php" class="sidebar-btn">
            <span class="sidebar-icon"><i class="fas fa-sign-out-alt"></i></span>
            Déconnexion
        </a>
    </aside>

    <main class="dashboard-main">
        <div class="dashboard-page-header">
            <div>
                <h2><i class="fas fa-chart-bar me-2" style="color:var(--teal);font-size:1.2rem"></i>Statistiques</h2>
                <p style="margin:0;font-size:.85rem;color:var(--gray-400)">Rapports de performance — Semaine en cours</p>
            </div>
            <div class="date-chip">
                <i class="fas fa-calendar-alt" style="color:var(--teal)"></i>
                <?php echo htmlspecialchars($today, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>

        <!-- Summary metrics -->
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="metric-card">
                    <div class="metric-icon teal"><i class="fas fa-hourglass-half"></i></div>
                    <div class="metric-label">En attente</div>
                    <div class="metric-value"><?php echo htmlspecialchars($summaryByStatus['pending'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="metric-sub">Cette semaine</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="metric-card">
                    <div class="metric-icon navy"><i class="fas fa-calendar-check"></i></div>
                    <div class="metric-label">Confirmés</div>
                    <div class="metric-value"><?php echo htmlspecialchars($summaryByStatus['confirmed'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="metric-sub">Cette semaine</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="metric-card">
                    <div class="metric-icon gold"><i class="fas fa-edit"></i></div>
                    <div class="metric-label">Modifiés</div>
                    <div class="metric-value"><?php echo $summaryByStatus['modified']; ?></div>
                    <div class="metric-sub">Cette semaine</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Status chart -->
            <div class="col-lg-6">
                <div class="stats-card">
                    <div class="stats-card-title">
                        <i class="fas fa-chart-pie"></i> Rendez-vous par statut (semaine)
                    </div>
                    <?php
                    $statusColors = ['pending' => 'teal', 'confirmed' => 'navy', 'modified' => 'gold'];
                    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmés', 'modified' => 'Modifiés'];
                    foreach ($summaryByStatus as $status => $count):
                        $pct = round($count / $totalWeekly * 100);
                    ?>
                        <div class="stat-bar-row">
                            <span class="stat-bar-label"><?php echo $statusLabels[$status]; ?></span>
                            <div class="stat-bar-track">
                                <div class="stat-bar-fill <?php echo $statusColors[$status]; ?>" style="width:<?php echo $pct; ?>%"></div>
                            </div>
                            <span class="stat-bar-value"><?php echo $count; ?></span>
                        </div>
                    <?php endforeach; ?>

                    <!-- Donut visual (CSS only) -->
                    <div class="d-flex justify-content-center mt-3">
                        <?php foreach ($summaryByStatus as $status => $count):
                            $pct = round($count / $totalWeekly * 100);
                            $colors = ['pending' => 'var(--teal)', 'confirmed' => 'var(--navy)', 'modified' => 'var(--gold)'];
                        ?>
                        <div class="text-center mx-3">
                            <div style="width:60px;height:60px;border-radius:50%;background:conic-gradient(<?php echo $colors[$status]; ?> <?php echo $pct*3.6; ?>deg, var(--gray-100) 0deg);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:<?php echo $status === 'confirmed' ? '#fff' : 'var(--navy)'; ?>">
                                <?php echo $pct; ?>%
                            </div>
                            <div style="font-size:.72rem;color:var(--gray-400);margin-top:.3rem"><?php echo $statusLabels[$status]; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Assignment chart -->
            <div class="col-lg-6">
                <div class="stats-card">
                    <div class="stats-card-title">
                        <i class="fas fa-random"></i> Mode d'assignation
                    </div>
                    <div class="stat-bar-row">
                        <span class="stat-bar-label">Auto-assigné</span>
                        <div class="stat-bar-track">
                            <div class="stat-bar-fill teal" style="width:<?php echo round($assignmentMap['auto']/$totalAssign*100); ?>%"></div>
                        </div>
                        <span class="stat-bar-value"><?php echo $assignmentMap['auto']; ?></span>
                    </div>
                    <div class="stat-bar-row">
                        <span class="stat-bar-label">Par réceptionniste</span>
                        <div class="stat-bar-track">
                            <div class="stat-bar-fill navy" style="width:<?php echo round($assignmentMap['receptionist']/$totalAssign*100); ?>%"></div>
                        </div>
                        <span class="stat-bar-value"><?php echo $assignmentMap['receptionist']; ?></span>
                    </div>

                    <div class="stats-card-title mt-4">
                        <i class="fas fa-exclamation-triangle"></i> Répartition par priorité
                    </div>
                    <?php
                    $prioColors = ['P1' => 'danger', 'P2' => 'gold', 'P3' => 'teal'];
                    $prioLabels = ['P1' => 'P1 — Urgent', 'P2' => 'P2 — Modéré', 'P3' => 'P3 — Routine'];
                    foreach ($priorityMap as $priority => $count):
                        $pct = round($count / $totalPriority * 100);
                    ?>
                        <div class="stat-bar-row">
                            <span class="stat-bar-label"><?php echo $prioLabels[$priority]; ?></span>
                            <div class="stat-bar-track">
                                <div class="stat-bar-fill <?php echo $prioColors[$priority]; ?>" style="width:<?php echo $pct; ?>%"></div>
                            </div>
                            <span class="stat-bar-value"><?php echo $count; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Unresolved flags table -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fas fa-flag me-2" style="color:var(--danger)"></i>Signalements non résolus</span>
                <span class="badge bg-danger"><?php echo count($unresolvedFlags); ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($unresolvedFlags)): ?>
                    <div class="alert alert-secondary m-3"><i class="fas fa-check-circle me-2"></i>Aucun signalement non résolu. Tout est à jour !</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date du créneau</th>
                                    <th>Commentaire du médecin</th>
                                    <th>Médecin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unresolvedFlags as $flag): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($flag['patient_name']); ?></strong></td>
                                        <td><span style="color:var(--gray-600)"><?php echo htmlspecialchars($flag['date'] ?? 'N/A'); ?></span></td>
                                        <td><?php echo htmlspecialchars($flag['comment']); ?></td>
                                        <td><?php echo htmlspecialchars($flag['doctor_name']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
