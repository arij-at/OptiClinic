<?php
require_once __DIR__ . '/../../includes/header.php';
$today = date('d M Y');
?>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-section-label">Principal</div>

        <button class="sidebar-btn active" type="button" data-bs-toggle="offcanvas" data-bs-target="#notificationsOffcanvas">
            <span class="sidebar-icon"><i class="fas fa-bell"></i></span>
            Notifications
            <?php if ($total_notifications > 0): ?>
                <span class="sidebar-badge"><?php echo htmlspecialchars($total_notifications, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
        </button>

        <a href="stats.php" class="sidebar-btn">
            <span class="sidebar-icon"><i class="fas fa-chart-bar"></i></span>
            Statistiques
        </a>

        <hr class="sidebar-divider">
        <div class="sidebar-section-label">Compte</div>

        <a href="logout.php" class="sidebar-btn sidebar-logout">
            <span class="sidebar-icon"><i class="fas fa-sign-out-alt"></i></span>
            Déconnexion
        </a>
    </aside>

    <!-- Main -->
    <main class="dashboard-main">
        <div class="dashboard-page-header">
            <div>
                <h2><i class="fas fa-th-large me-2" style="color:var(--teal);font-size:1.2rem"></i>Tableau de bord</h2>
                <p style="margin:0;font-size:.85rem;color:var(--gray-400)">Réceptionniste — Gestion des rendez-vous</p>
            </div>
            <div class="date-chip">
                <i class="fas fa-calendar-alt" style="color:var(--teal)"></i>
                <?php echo htmlspecialchars($today, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>

        <?php if ($assigning_form): ?>
            <div class="alert alert-info mb-4">
                <i class="fas fa-user-plus me-2"></i>
                <strong>Assignation en cours :</strong> Sélectionnez un créneau pour <strong><?php echo htmlspecialchars($assigning_form['name']); ?></strong>
                <a href="?cancel_assign=1" class="btn btn-sm btn-secondary ms-3">
                    <i class="fas fa-times me-1"></i>Annuler
                </a>
            </div>
        <?php endif; ?>

        <!-- Metric cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-icon teal"><i class="fas fa-hourglass-half"></i></div>
                    <div class="metric-label">En attente</div>
                    <div class="metric-value"><?php echo count($pending_submissions); ?></div>
                    <div class="metric-sub">Soumissions à traiter</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-icon danger"><i class="fas fa-flag"></i></div>
                    <div class="metric-label">Signalements</div>
                    <div class="metric-value"><?php echo count($doctor_requests); ?></div>
                    <div class="metric-sub">Demandes médecins</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-icon navy"><i class="fas fa-calendar-check"></i></div>
                    <div class="metric-label">Confirmés</div>
                    <div class="metric-value"><?php echo count($confirmed_appointments); ?></div>
                    <div class="metric-sub">Rendez-vous actifs</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-icon gold"><i class="fas fa-bell"></i></div>
                    <div class="metric-label">Total alertes</div>
                    <div class="metric-value"><?php echo htmlspecialchars($total_notifications, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="metric-sub">Notifications actives</div>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-calendar-alt" style="color:var(--teal)"></i>
                Calendrier des créneaux
            </div>
            <div class="card-body p-3">
                <?php require_once __DIR__ . '/../../includes/shared_calendar.php'; ?>
            </div>
        </div>
    </main>
</div>

<!-- Notifications Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="notificationsOffcanvas" style="width:420px">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title"><i class="fas fa-bell me-2" style="color:var(--teal)"></i>Notifications</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body" style="padding:1.2rem">

        <!-- Filters -->
        <form method="get" class="mb-4">
            <input type="hidden" name="appt_date_from" value="<?php echo htmlspecialchars($apptDateFrom ?? ''); ?>">
            <input type="hidden" name="appt_date_to" value="<?php echo htmlspecialchars($apptDateTo ?? ''); ?>">
            <input type="hidden" name="appt_status" value="<?php echo htmlspecialchars($apptStatus ?? 'all'); ?>">
            <p class="form-label mb-2"><i class="fas fa-filter me-1" style="color:var(--teal)"></i><strong>Filtres</strong></p>
            <div class="row g-2">
                <div class="col-12">
                    <input type="text" name="search_name" class="form-control" placeholder="Rechercher par nom..." value="<?php echo htmlspecialchars($searchName ?? ''); ?>">
                </div>
                <div class="col-6">
                    <select name="filter_priority" class="form-select">
                        <option value="">Toutes priorités</option>
                        <option value="P1"<?php echo (isset($filterPriority) && $filterPriority === 'P1') ? ' selected' : ''; ?>>🔴 P1 — Urgent</option>
                        <option value="P2"<?php echo (isset($filterPriority) && $filterPriority === 'P2') ? ' selected' : ''; ?>>🟡 P2 — Modéré</option>
                        <option value="P3"<?php echo (isset($filterPriority) && $filterPriority === 'P3') ? ' selected' : ''; ?>>🟢 P3 — Routine</option>
                    </select>
                </div>
                <div class="col-6">
                    <select name="filter_routing" class="form-select">
                        <option value="">Tout routage</option>
                        <option value="auto"<?php echo (isset($filterRouting) && $filterRouting === 'auto') ? ' selected' : ''; ?>>Auto</option>
                        <option value="receptionist"<?php echo (isset($filterRouting) && $filterRouting === 'receptionist') ? ' selected' : ''; ?>>Manuel</option>
                    </select>
                </div>
                <div class="col-8">
                    <button type="submit" class="btn btn-primary w-100">Appliquer</button>
                </div>
                <div class="col-4">
                    <a href="dashboard.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>

        <!-- Pending submissions -->
        <div class="mb-3">
            <p class="form-label mb-2"><i class="fas fa-hourglass-half me-1" style="color:var(--teal)"></i><strong><?php echo htmlspecialchars($lang_strings['pending_submissions'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span class="badge bg-danger ms-1"><?php echo count($pending_submissions); ?></span>
            </p>
            <?php if (empty($pending_submissions)): ?>
                <div class="alert alert-secondary">Aucune soumission en attente.</div>
            <?php else: ?>
                <?php foreach ($pending_submissions as $sub): ?>
                    <div class="notification-item">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <div>
                                <strong><?php echo htmlspecialchars($sub['name']); ?></strong>
                                <span class="priority-badge <?php echo htmlspecialchars($sub['priority'], ENT_QUOTES, 'UTF-8'); ?> ms-2"><?php echo htmlspecialchars($sub['priority'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($sub['triage_score'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                        <p class="mt-1"><?php echo htmlspecialchars($sub['symptoms_description'] ?: 'Aucune description fournie'); ?></p>
                        <?php if ($sub['preferred_slot_id']): ?>
                            <p style="font-size:.8rem;color:var(--teal);margin:.2rem 0 0">
                                <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($sub['date'] . ' à ' . $sub['start_time']); ?>
                            </p>
                        <?php endif; ?>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <span style="font-size:.78rem;color:var(--gray-400)">
                                <?php echo $sub['preferred_slot_id'] ? $lang_strings['auto_assigned'] : $lang_strings['manual_assign']; ?>
                            </span>
                            <a href="?assign=<?php echo htmlspecialchars($sub['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-calendar-plus me-1"></i><?php echo htmlspecialchars($lang_strings['assign_slot'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Doctor requests -->
        <div class="mb-3">
            <p class="form-label mb-2"><i class="fas fa-flag me-1" style="color:var(--danger)"></i><strong><?php echo htmlspecialchars($lang_strings['doctor_requests'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span class="badge bg-danger ms-1"><?php echo count($doctor_requests); ?></span>
            </p>
            <?php if (empty($doctor_requests)): ?>
                <div class="alert alert-secondary">Aucune demande médecin.</div>
            <?php else: ?>
                <?php foreach ($doctor_requests as $req): ?>
                    <div class="notification-item">
                        <strong><?php echo htmlspecialchars($req['name']); ?></strong>
                        <span style="font-size:.8rem;color:var(--gray-400);display:block"><?php echo htmlspecialchars($req['date'] . ' à ' . $req['start_time']); ?></span>
                        <p><?php echo htmlspecialchars($req['comment']); ?></p>
                        <form method="post" action="resolve_flag.php" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                            <input type="hidden" name="flag_id" value="<?php echo htmlspecialchars($req['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i><?php echo htmlspecialchars($lang_strings['modify_slot'], ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Confirmed appointments -->
        <div>
            <p class="form-label mb-2"><i class="fas fa-calendar-check me-1" style="color:var(--teal)"></i><strong>Rendez-vous confirmés</strong></p>
            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="search_name" value="<?php echo htmlspecialchars($searchName ?? ''); ?>">
                <input type="hidden" name="filter_priority" value="<?php echo htmlspecialchars($filterPriority ?? ''); ?>">
                <input type="hidden" name="filter_routing" value="<?php echo htmlspecialchars($filterRouting ?? ''); ?>">
                <div class="col-6">
                    <label class="form-label" style="font-size:.75rem">Date début</label>
                    <input type="date" name="appt_date_from" class="form-control" value="<?php echo htmlspecialchars($apptDateFrom ?? ''); ?>">
                </div>
                <div class="col-6">
                    <label class="form-label" style="font-size:.75rem">Date fin</label>
                    <input type="date" name="appt_date_to" class="form-control" value="<?php echo htmlspecialchars($apptDateTo ?? ''); ?>">
                </div>
                <div class="col-8">
                    <select name="appt_status" class="form-select">
                        <option value="all"<?php echo (!isset($apptStatus) || $apptStatus === 'all') ? ' selected' : ''; ?>>Tous les statuts</option>
                        <option value="pending"<?php echo (isset($apptStatus) && $apptStatus === 'pending') ? ' selected' : ''; ?>>En attente</option>
                        <option value="confirmed"<?php echo (isset($apptStatus) && $apptStatus === 'confirmed') ? ' selected' : ''; ?>>Confirmé</option>
                        <option value="modified"<?php echo (isset($apptStatus) && $apptStatus === 'modified') ? ' selected' : ''; ?>>Modifié</option>
                    </select>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary w-100" style="margin-top:1.6rem">Filtrer</button>
                </div>
            </form>
            <?php if (empty($confirmed_appointments)): ?>
                <div class="alert alert-secondary">Aucun rendez-vous trouvé.</div>
            <?php else: ?>
                <?php foreach ($confirmed_appointments as $appt): ?>
                    <div class="notification-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong><?php echo htmlspecialchars($appt['name']); ?></strong>
                            <span class="badge bg-info"><?php echo htmlspecialchars($appt['status']); ?></span>
                        </div>
                        <div style="font-size:.82rem;color:var(--gray-600);margin-top:.3rem">
                            <?php echo $appt['date'] ? htmlspecialchars($appt['date'] . ' · ' . $appt['start_time'] . ' – ' . $appt['end_time']) : 'Pas de créneau assigné'; ?>
                        </div>
                        <div style="font-size:.78rem;color:var(--gray-400);margin-top:.2rem">
                            Priorité : <?php echo htmlspecialchars($appt['priority']); ?> · Routage : <?php echo htmlspecialchars($appt['routing']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
