<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$token = $_GET['token'] ?? '';
if (!$token) {
    echo '<div class="container mt-5"><div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Lien invalide ou expiré.</div></div>';
    require_once 'includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, a.*, t.date, t.start_time, t.end_time
    FROM patients p
    JOIN appointments a ON p.id = a.patient_id
    LEFT JOIN time_slots t ON a.slot_id = t.id
    WHERE p.token = ?
");
$stmt->execute([$token]);
$data = $stmt->fetch();

if (!$data || $data['status'] !== 'confirmed') { ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="appointment-card text-center" style="padding:3rem">
                <div style="width:80px;height:80px;background:var(--teal-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2rem;color:var(--teal)">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Rendez-vous en attente</h3>
                <p style="color:var(--gray-600);margin-top:.5rem"><?php echo $lang_strings['appointment_not_confirmed']; ?></p>
                <a href="index.php" class="btn btn-primary mt-3"><i class="fas fa-home me-2"></i>Retour à l'accueil</a>
            </div>
        </div>
    </div>
</div>
<?php
    require_once 'includes/footer.php';
    exit;
}

$appointment_date = $data['date'];
$month      = date('m', strtotime($appointment_date));
$year       = date('Y', strtotime($appointment_date));
$firstDay   = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date('t', $firstDay);
$dayOfWeek  = (int)date('N', $firstDay); // 1=Mon … 7=Sun
$confirmedDay = (int)date('j', strtotime($appointment_date));
$monthName  = date('F Y', $firstDay);
$dayNames   = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
?>

<div class="container mt-5 mb-5">
    <?php if ($data['status'] === 'modified'): ?>
        <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Attention :</strong> <?php echo $lang_strings['appointment_modified']; ?>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Banner -->
            <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3"
                 style="background:var(--teal-light);border:1px solid rgba(0,169,157,.3)">
                <div style="width:48px;height:48px;background:var(--teal);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.3rem;flex-shrink:0">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p style="margin:0;font-weight:600;color:var(--navy)">Rendez-vous confirmé</p>
                    <p style="margin:0;font-size:.88rem;color:var(--teal-dark)">Veuillez vous présenter 10 minutes avant l'heure prévue.</p>
                </div>
            </div>

            <div class="appointment-card">
                <!-- Header -->
                <div class="appointment-header">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:56px;height:56px;background:rgba(255,255,255,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border:2px solid rgba(255,255,255,.2)">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h3><?php echo htmlspecialchars($data['name']); ?></h3>
                            <span style="color:rgba(255,255,255,.6);font-size:.85rem">Dossier patient</span>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="appointment-body">
                    <div class="row g-4">
                        <!-- Patient details -->
                        <div class="col-md-6">
                            <h6 class="mb-3" style="color:var(--gray-400);font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;font-family:'DM Sans',sans-serif">
                                <i class="fas fa-user me-1" style="color:var(--teal)"></i> Informations patient
                            </h6>
                            <div class="appointment-detail-row">
                                <div class="appointment-detail-icon"><i class="fas fa-birthday-cake"></i></div>
                                <div>
                                    <div class="appointment-detail-label">Date de naissance</div>
                                    <div class="appointment-detail-value"><?php echo htmlspecialchars($data['date_of_birth']); ?></div>
                                </div>
                            </div>
                            <div class="appointment-detail-row">
                                <div class="appointment-detail-icon"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <div class="appointment-detail-label">Email</div>
                                    <div class="appointment-detail-value"><?php echo htmlspecialchars($data['email']); ?></div>
                                </div>
                            </div>
                            <div class="appointment-detail-row">
                                <div class="appointment-detail-icon"><i class="fas fa-phone"></i></div>
                                <div>
                                    <div class="appointment-detail-label">Téléphone</div>
                                    <div class="appointment-detail-value"><?php echo htmlspecialchars($data['phone']); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment details -->
                        <div class="col-md-6">
                            <h6 class="mb-3" style="color:var(--gray-400);font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;font-family:'DM Sans',sans-serif">
                                <i class="fas fa-calendar-check me-1" style="color:var(--teal)"></i> Détails du rendez-vous
                            </h6>
                            <div class="appointment-detail-row">
                                <div class="appointment-detail-icon"><i class="fas fa-calendar-day"></i></div>
                                <div>
                                    <div class="appointment-detail-label">Date</div>
                                    <div class="appointment-detail-value"><?php echo htmlspecialchars($appointment_date); ?></div>
                                </div>
                            </div>
                            <div class="appointment-detail-row">
                                <div class="appointment-detail-icon"><i class="fas fa-clock"></i></div>
                                <div>
                                    <div class="appointment-detail-label">Heure</div>
                                    <div class="appointment-detail-value">
                                        <?php echo htmlspecialchars($data['start_time']); ?> – <?php echo htmlspecialchars($data['end_time']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="appointment-detail-row">
                                <div class="appointment-detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <div class="appointment-detail-label">Lieu</div>
                                    <div class="appointment-detail-value">OptiClinic — Tunis</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mini Calendar -->
                    <div class="mt-4">
                        <h6 style="color:var(--gray-400);font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;font-family:'DM Sans',sans-serif;margin-bottom:1rem">
                            <i class="fas fa-calendar me-1" style="color:var(--teal)"></i>
                            <?php echo ucfirst($monthName); ?>
                        </h6>

                        <div style="background:var(--off-white);border-radius:var(--radius-md);padding:1rem;overflow-x:auto">
                            <table style="width:100%;border-collapse:separate;border-spacing:3px">
                                <thead>
                                    <tr>
                                        <?php foreach ($dayNames as $d): ?>
                                            <th style="text-align:center;font-size:.72rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;padding:.4rem">
                                                <?php echo $d; ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $cellCount = 0;
                                echo '<tr>';
                                // Lead-in blank cells (Mon=1, so offset = dayOfWeek-1)
                                for ($i = 1; $i < $dayOfWeek; $i++) {
                                    echo '<td style="padding:.4rem"></td>';
                                    $cellCount++;
                                }
                                for ($day = 1; $day <= $daysInMonth; $day++) {
                                    if ($day == $confirmedDay) {
                                        $style = 'background:var(--teal);color:#fff;font-weight:700;';
                                    } else {
                                        $style = 'color:var(--navy);';
                                    }
                                    echo "<td style=\"text-align:center;padding:.4rem;border-radius:6px;font-size:.82rem;{$style}\">{$day}</td>";
                                    $cellCount++;
                                    if ($cellCount % 7 === 0 && $day < $daysInMonth) {
                                        echo '</tr><tr>';
                                    }
                                }
                                // Trailing blanks
                                while ($cellCount % 7 !== 0) {
                                    echo '<td></td>';
                                    $cellCount++;
                                }
                                echo '</tr>';
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer note -->
                    <div class="mt-4 p-3 rounded-3 d-flex gap-3 align-items-start"
                         style="background:var(--gold-light);border:1px solid rgba(240,165,0,.3)">
                        <i class="fas fa-lightbulb mt-1" style="color:var(--gold);flex-shrink:0"></i>
                        <p style="margin:0;font-size:.85rem;color:var(--gray-800)">
                            <strong>Rappel :</strong> Munissez-vous de votre carte d'identité et de votre carnet de santé.
                            En cas d'empêchement, contactez-nous au <strong>+216 71 234 567</strong> au moins 24h à l'avance.
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home me-2"></i>Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
