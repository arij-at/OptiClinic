<?php
/**
 * Unified Calendar Component for all user types
 * Shows available slots for patients, confirmed appointments for staff
 * Handles role-based interactions (patient selection, receptionist modification, doctor flagging)
 */

// Get role from session
$user_role = $_SESSION['role'] ?? 'patient';
$patient_id = $_SESSION['patient_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Check if receptionist is assigning
$assigning_form_id = $_SESSION['assigning_form_id'] ?? null;
$is_assigning = $user_role === 'receptionist' && $assigning_form_id;

// Get current month/year or from GET
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// Build calendar dates
$firstDay = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date('t', $firstDay);
$dayOfWeek = date('w', $firstDay);
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}
$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// Helper: get available slots for a date (for patients)
function getAvailableSlots($pdo, $date) {
    $stmt = $pdo->prepare("SELECT id, start_time, end_time FROM time_slots WHERE date = ? AND is_available = 1 ORDER BY start_time");
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Helper: check if date has available slots
function hasAvailableSlots($pdo, $date) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM time_slots WHERE date = ? AND is_available = 1");
    $stmt->execute([$date]);
    return $stmt->fetchColumn() > 0;
}

// Helper: get appointments for a date (for staff)
function getAppointmentsForDate($pdo, $date) {
    $stmt = $pdo->prepare("
        SELECT a.id, a.patient_id, a.status, a.priority, a.form_id, p.name, t.start_time, t.end_time, sf.comment, sf.resolved
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        LEFT JOIN time_slots t ON a.slot_id = t.id OR a.preferred_slot_id = t.id
        LEFT JOIN slot_flags sf ON a.id = sf.appointment_id AND sf.resolved = 0
        WHERE t.date = ? AND a.status = 'confirmed'
        ORDER BY t.start_time
    ");
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get appointments by date for month view (staff)
if ($user_role !== 'patient') {
    $stmt = $pdo->prepare("
        SELECT a.id, a.patient_id, a.status, a.priority, p.name, t.date, t.start_time, t.end_time, sf.id as flag_id, sf.comment
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN time_slots t ON a.slot_id = t.id OR a.preferred_slot_id = t.id
        LEFT JOIN slot_flags sf ON a.id = sf.appointment_id AND sf.resolved = 0
        WHERE a.status = 'confirmed' AND t.date >= ? AND t.date <= ?
        ORDER BY t.date, t.start_time
    ");
    $stmt->execute([date('Y-m-01', $firstDay), date('Y-m-t', $firstDay)]);
    $confirmed_appts = $stmt->fetchAll();

    $appts_by_date = [];
    $flagged_dates = [];
    foreach ($confirmed_appts as $appt) {
        $appts_by_date[$appt['date']][] = $appt;
        if ($appt['flag_id']) {
            $flagged_dates[$appt['date']] = true;
        }
    }
}
?>

<div class="calendar-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="?month=<?php echo htmlspecialchars($prevMonth, ENT_QUOTES, 'UTF-8'); ?>&year=<?php echo htmlspecialchars($prevYear, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary">
            &laquo; Précédent
        </a>
        <h4 class="mb-0"><?php echo date('F Y', $firstDay); ?></h4>
        <a href="?month=<?php echo htmlspecialchars($nextMonth, ENT_QUOTES, 'UTF-8'); ?>&year=<?php echo htmlspecialchars($nextYear, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary">
            Suivant &raquo;
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered calendar-table">
            <thead>
                <tr>
                    <th class="text-center">Lun</th>
                    <th class="text-center">Mar</th>
                    <th class="text-center">Mer</th>
                    <th class="text-center">Jeu</th>
                    <th class="text-center">Ven</th>
                    <th class="text-center">Sam</th>
                    <th class="text-center">Dim</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $dayCounter = 1;
                $emptyDays = $dayOfWeek;
                $totalCells = $emptyDays + $daysInMonth;
                $weeks = ceil($totalCells / 7);

                for ($week = 0; $week < $weeks; $week++) {
                    echo '<tr style="height: 120px;">';
                    for ($dow = 0; $dow < 7; $dow++) {
                        if ($week === 0 && $dow < $emptyDays) {
                            echo '<td class="bg-light"></td>';
                        } elseif ($dayCounter <= $daysInMonth) {
                            $date = sprintf('%04d-%02d-%02d', $year, $month, $dayCounter);
                            $isPast = strtotime($date) < strtotime(date('Y-m-d'));

                            if ($user_role === 'patient' || $is_assigning) {
                                // PATIENT VIEW or ASSIGNING: Show available slots
                                $hasSlots = !$isPast && hasAvailableSlots($pdo, $date);
                                $cellClass = 'calendar-day ';
                                if ($isPast) {
                                    $cellClass .= 'bg-light text-muted cursor-not-allowed';
                                } elseif ($hasSlots) {
                                    $cellClass .= 'bg-success text-white available-day';
                                } else {
                                    $cellClass .= 'bg-secondary text-white';
                                }
                                echo '<td class="' . $cellClass . '" data-date="' . $date . '">';
                                echo '<strong>' . $dayCounter . '</strong>';
                                if (!$isPast && $hasSlots) {
                                    echo '<br><small class="badge bg-light text-dark">Disponible</small>';
                                }
                                echo '</td>';
                            } else {
                                // STAFF VIEW: Show appointments
                                $day_appts = $appts_by_date[$date] ?? [];
                                $hasFlags = isset($flagged_dates[$date]);
                                $cellClass = 'calendar-day cursor-pointer text-decoration-underline';
                                if ($hasFlags) {
                                    $cellClass .= ' position-relative';
                                }
                                echo '<td class="' . $cellClass . '" data-date="' . $date . '" data-bs-toggle="modal" data-bs-target="#appointmentsModal">';
                                echo '<strong>' . $dayCounter . '</strong>';
                                if ($hasFlags) {
                                    echo '<span class="badge bg-danger position-absolute top-0 end-0" style="font-size: 18px; padding: 2px 6px; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center;">!</span>';
                                }
                                echo '<br>';
                                foreach ($day_appts as $appt) {
                                    $initials = strtoupper(substr($appt['name'], 0, 2));
                                    $color = $appt['priority'] === 'P1' ? 'danger' : ($appt['priority'] === 'P2' ? 'warning' : 'success');
                                    echo '<span class="badge bg-' . $color . ' me-1" title="' . htmlspecialchars($appt['name']) . '">' . $initials . '</span>';
                                }
                                echo '</td>';
                            }
                            $dayCounter++;
                        } else {
                            echo '<td class="bg-light"></td>';
                        }
                    }
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($user_role === 'patient' || $is_assigning): ?>
    <!-- Patient or Assigning: Slot Selection Modal -->
    <div class="modal fade" id="slotsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créneaux disponibles - <span id="selectedDate"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="slotsContainer" class="slot-buttons">
                        <!-- Slots will be inserted here via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmSlotBtn" disabled>Confirmer ce créneau</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    const isAssigning = <?php echo $is_assigning ? 'true' : 'false'; ?>;
    let modifySelectedSlotId = null;
    
    function submitModifySlot() {
        if (!modifySelectedSlotId) return;
        const appointmentId = document.getElementById('modifyAppointmentId').value;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'modify_appointment.php';
        const inputApptId = document.createElement('input');
        inputApptId.type = 'hidden';
        inputApptId.name = 'appointment_id';
        inputApptId.value = appointmentId;
        form.appendChild(inputApptId);
        const inputSlotId = document.createElement('input');
        inputSlotId.type = 'hidden';
        inputSlotId.name = 'new_slot_id';
        inputSlotId.value = modifySelectedSlotId;
        form.appendChild(inputSlotId);
        const inputCsrf = document.createElement('input');
        inputCsrf.type = 'hidden';
        inputCsrf.name = 'csrf_token';
        inputCsrf.value = '<?php echo htmlspecialchars(csrf_token()); ?>';
        form.appendChild(inputCsrf);
        document.body.appendChild(form);
        form.submit();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const availableDays = document.querySelectorAll('.available-day');
        let selectedSlotId = null;
        
        // Determine API base path based on current location
        const currentPath = window.location.pathname;
        const apiBase = currentPath.includes('/receptionist/') || currentPath.includes('/doctor/') ? '../' : '';

        availableDays.forEach(day => {
            day.addEventListener('click', function() {
                const date = this.dataset.date;
                const slotsModal = new bootstrap.Modal(document.getElementById('slotsModal'));
                document.getElementById('selectedDate').textContent = date;

                // Fetch slots for this date
                fetch(`${apiBase}get_slots.php?date=${date}`)
                    .then(response => response.json())
                    .then(slots => {
                        const container = document.getElementById('slotsContainer');
                        container.innerHTML = '';
                        selectedSlotId = null;
                        document.getElementById('confirmSlotBtn').disabled = true;

                        if (!slots || slots.length === 0) {
                            container.innerHTML = '<p class="text-muted">Aucun créneau pour cette date.</p>';
                            return;
                        }

                        let availableCount = 0;
                        slots.forEach(slot => {
                            // Skip unavailable slots for both patients and assigning staff
                            if (!slot.is_available) return;
                            availableCount++;
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = slot.start_time + ' – ' + slot.end_time;
                            btn.dataset.slotId = slot.id;
                            btn.className = 'btn btn-outline-primary m-2';
                            btn.addEventListener('click', function() {
                                // Remove previous selection
                                document.querySelectorAll('#slotsContainer .btn').forEach(b => {
                                    if (b.classList.contains('btn-primary')) {
                                        b.classList.remove('btn-primary');
                                        b.classList.add('btn-outline-primary');
                                    }
                                });
                                // Set new selection
                                this.classList.remove('btn-outline-primary');
                                this.classList.add('btn-primary');
                                selectedSlotId = slot.id;
                                document.getElementById('confirmSlotBtn').disabled = false;
                            });
                            container.appendChild(btn);
                        });

                        // If no available slots were found, show message
                        if (availableCount === 0) {
                            container.innerHTML = '<p class="text-muted">Aucun créneau disponible pour cette date.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('slotsContainer').innerHTML = '<p class="text-danger">Erreur lors du chargement des créneaux.</p>';
                    });

                slotsModal.show();
            });
        });

        document.getElementById('confirmSlotBtn').addEventListener('click', function() {
            if (!selectedSlotId) return;
            // Hide the modal before submitting
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('slotsModal'));
            if (modalInstance) modalInstance.hide();
            const form = document.createElement('form');
            form.method = 'POST';
            <?php if ($is_assigning): ?>
                form.action = 'assign.php';
                const inputFormId = document.createElement('input');
                inputFormId.type = 'hidden';
                inputFormId.name = 'form_id';
                inputFormId.value = '<?php echo htmlspecialchars($assigning_form_id, ENT_QUOTES, 'UTF-8'); ?>';
                form.appendChild(inputFormId);
            <?php else: ?>
                form.action = `${apiBase}save_preference.php`;
            <?php endif; ?>
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'slot_id';
            input.value = selectedSlotId;
            form.appendChild(input);
            const inputCsrf = document.createElement('input');
            inputCsrf.type = 'hidden';
            inputCsrf.name = 'csrf_token';
            inputCsrf.value = '<?php echo htmlspecialchars(csrf_token()); ?>';
            form.appendChild(inputCsrf);
            document.body.appendChild(form);
            form.submit();
        });
    });
    </script>

    <!-- Receptionist: Modify Appointment Modal -->
    <div class="modal fade" id="modifySlotsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le créneau du rendez-vous</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Sélectionnez un nouveau créneau pour ce rendez-vous.</p>
                    <div id="modifySlotsContainer" class="slot-buttons">
                        <!-- Slots will be inserted here via AJAX -->
                    </div>
                    <input type="hidden" id="modifyAppointmentId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-warning" id="modifyConfirmSlotBtn" disabled onclick="submitModifySlot()">Confirmer le changement</button>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Staff: Appointments Modal -->
    <div class="modal fade" id="appointmentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rendez-vous - <span id="modalDate"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="appointmentsContainer">
                    <!-- Appointments will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.calendar-day').forEach(dayCell => {
            dayCell.addEventListener('click', function() {
                const date = this.dataset.date;
                const modal = document.getElementById('appointmentsModal');
                document.getElementById('modalDate').textContent = date;
                
                // Determine API base path based on current location
                const currentPath = window.location.pathname;
                const apiBase = currentPath.includes('/receptionist/') || currentPath.includes('/doctor/') ? '../' : '';

                // Fetch appointments for this date
                fetch(`${apiBase}get_appointments.php?date=${date}`)
                    .then(response => response.json())
                    .then(appointments => {
                        const container = document.getElementById('appointmentsContainer');
                        container.innerHTML = '';

                        if (!appointments || appointments.length === 0) {
                            container.innerHTML = '<p class="text-muted">Aucun rendez-vous pour cette date.</p>';
                            return;
                        }

                        appointments.forEach(appt => {
                            const statusBadge = appt.status === 'confirmed' ? '<span class="badge bg-success">Confirmé</span>' : '<span class="badge bg-warning">Modifié</span>';
                            const priorityBadge = 'bg-' + (appt.priority === 'P1' ? 'danger' : (appt.priority === 'P2' ? 'warning' : 'success'));

                            let content = `
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6>${appt.name}</h6>
                                                <small>
                                                    <span class="badge ${priorityBadge}">${appt.priority}</span>
                                                    ${statusBadge}
                                                </small>
                            `;
                            
                            // Add pre-diagnosis section for doctor view
                            const role = '<?php echo htmlspecialchars($user_role, ENT_QUOTES, 'UTF-8'); ?>';
                            if (role === 'doctor') {
                                content += `
                                    <div class="mt-2 pt-2 border-top">
                                        <strong>Pré-diagnostic:</strong>
                                        <ul class="small mb-0 mt-1">
                                            <li><strong>Score de triage:</strong> ${appt.triage_score}</li>
                                            <li><strong>Oeil affecté:</strong> ${appt.q_which_eye || 'N/A'}</li>
                                            <li><strong>Durée:</strong> ${appt.q_how_long || 'N/A'}</li>
                                            <li><strong>Douleur (0-4):</strong> ${appt.q_pain_level}</li>
                                            <li><strong>Rougeur (0-4):</strong> ${appt.q_redness}</li>
                                            <li><strong>Écoulement (0-4):</strong> ${appt.q_discharge}</li>
                                            <li><strong>Vision (0-4):</strong> ${appt.q_vision}</li>
                                        </ul>
                                        ${appt.symptoms_description ? '<p class="small mt-2"><strong>Description:</strong> ' + appt.symptoms_description + '</p>' : ''}
                                    </div>
                                `;
                            }
                            
                            content += `
                                            </div>
                                            <div class="text-end">
                                                <strong>${appt.start_time} – ${appt.end_time}</strong>
                                            </div>
                                        </div>
                            `;

                            // Role-specific actions
                            if (role === 'receptionist') {
                                if (appt.flag_id) {
                                    content += `
                                        <div class="alert alert-warning mt-2">
                                            <strong>⚠️ Signalement du docteur:</strong>
                                            <p class="mb-1">${appt.comment || 'Aucun commentaire'}</p>
                                            <button class="btn btn-sm btn-danger" onclick="removeFlag(${appt.flag_id})">Supprimer le signalement</button>
                                        </div>
                                    `;
                                }
                                content += `
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-warning" onclick="editAppointment(${appt.id})">Modifier le créneau</button>
                                    </div>
                                `;
                            } else if (role === 'doctor') {
                                content += `
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-danger" onclick="flagAppointment(${appt.id})">Signaler un changement</button>
                                    </div>
                                `;
                                if (appt.comment) {
                                    content += `<div class="alert alert-warning mt-2"><strong>Commentaire:</strong> ${appt.comment}</div>`;
                                }
                            }

                            content += `</div></div>`;
                            container.innerHTML += content;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('appointmentsContainer').innerHTML = '<p class="text-danger">Erreur lors du chargement des rendez-vous.</p>';
                    });

                new bootstrap.Modal(modal).show();
            });
        });
    });

    function editAppointment(appointmentId) {
        document.getElementById('modifyAppointmentId').value = appointmentId;
        document.getElementById('modifySlotsContainer').innerHTML = '';
        document.getElementById('modifyConfirmSlotBtn').disabled = true;
        modifySelectedSlotId = null;
        const modal = new bootstrap.Modal(document.getElementById('modifySlotsModal'));
        
        // Fetch all available slots
        const currentPath = window.location.pathname;
        const apiBase = currentPath.includes('/receptionist/') || currentPath.includes('/doctor/') ? '../' : '';
        
        fetch(`${apiBase}get_slots.php?date=all`)
            .then(response => response.json())
            .then(slots => {
                const container = document.getElementById('modifySlotsContainer');
                container.innerHTML = '';
                
                if (!slots || slots.length === 0) {
                    container.innerHTML = '<p class="text-muted">Aucun créneau disponible.</p>';
                    return;
                }
                
                // Group slots by date
                const slotsByDate = {};
                slots.forEach(slot => {
                    if (!slotsByDate[slot.date]) {
                        slotsByDate[slot.date] = [];
                    }
                    slotsByDate[slot.date].push(slot);
                });
                
                // Display slots grouped by date
                Object.keys(slotsByDate).sort().forEach(date => {
                    const dateLabel = document.createElement('div');
                    dateLabel.className = 'mb-3';
                    dateLabel.innerHTML = `<strong>${date}</strong><br>`;
                    container.appendChild(dateLabel);
                    
                    slotsByDate[date].forEach(slot => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = slot.is_available ? 'btn btn-outline-primary m-2' : 'btn btn-secondary m-2 disabled';
                        btn.textContent = slot.start_time + ' – ' + slot.end_time;
                        btn.disabled = !slot.is_available;
                        
                        if (slot.is_available) {
                            btn.addEventListener('click', function() {
                                document.querySelectorAll('#modifySlotsContainer .btn').forEach(b => {
                                    if (b.classList.contains('btn-primary')) {
                                        b.classList.remove('btn-primary');
                                        b.classList.add('btn-outline-primary');
                                    }
                                });
                                this.classList.remove('btn-outline-primary');
                                this.classList.add('btn-primary');
                                modifySelectedSlotId = slot.id;
                                document.getElementById('modifyConfirmSlotBtn').disabled = false;
                            });
                        }
                        container.appendChild(btn);
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modifySlotsContainer').innerHTML = '<p class="text-danger">Erreur lors du chargement des créneaux.</p>';
            });
        
        modal.show();
    }

    function removeFlag(flagId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce signalement ?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'remove_flag.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'flag_id';
            input.value = flagId;
            form.appendChild(input);
            const inputCsrf = document.createElement('input');
            inputCsrf.type = 'hidden';
            inputCsrf.name = 'csrf_token';
            inputCsrf.value = '<?php echo htmlspecialchars(csrf_token()); ?>';
            form.appendChild(inputCsrf);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function flagAppointment(appointmentId) {
        const flagCommentInput = document.getElementById('flagCommentInput');
        flagCommentInput.value = '';
        document.getElementById('flagAppointmentId').value = appointmentId;
        const modal = new bootstrap.Modal(document.getElementById('flagCommentModal'));
        modal.show();
    }
    
    function submitFlagComment() {
        const appointmentId = document.getElementById('flagAppointmentId').value;
        const comment = document.getElementById('flagCommentInput').value.trim();
        if (!comment) {
            alert('Veuillez entrer un commentaire');
            return;
        }
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'flag.php';
        const inputApptId = document.createElement('input');
        inputApptId.type = 'hidden';
        inputApptId.name = 'appointment_id';
        inputApptId.value = appointmentId;
        form.appendChild(inputApptId);
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

    function resolveFlag(appointmentId) {
        alert('Resolve flag for appointment ' + appointmentId + ' - To be implemented');
        // TODO: Implement flag resolution
    }
    </script>

<?php if ($user_role === 'doctor'): ?>
    <!-- Doctor: Flag Comment Modal -->
    <div class="modal fade" id="flagCommentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Signaler un changement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="flagCommentInput" class="form-label">Commentaire <span class="text-danger">*</span></label>
                    <textarea id="flagCommentInput" class="form-control" rows="4" placeholder="Décrivez les changements nécessaires..."></textarea>
                    <input type="hidden" id="flagAppointmentId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" onclick="submitFlagComment()">Envoyer le signalement</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php endif; ?>

<style>
.calendar-wrapper {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.calendar-table {
    border-collapse: collapse;
}

.calendar-table td {
    vertical-align: top;
    padding: 8px;
    font-size: 14px;
}

.calendar-day {
    border: 1px solid #dee2e6;
    cursor: default;
}

.calendar-day.cursor-pointer {
    cursor: pointer;
    transition: background-color 0.2s;
}

.calendar-day.cursor-pointer:hover {
    background-color: #f8f9fa;
}

.calendar-day.available-day {
    cursor: pointer;
}

.slot-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.slot-buttons .btn {
    flex: 1;
    min-width: 120px;
}
</style>
