<?php
class AppointmentModel
{
    protected $pdo;

    /**
     * Constructeur de la classe AppointmentModel
     * @param PDO $pdo Instance de PDO pour la connexion à la base de données
     * @return void
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Créer un rendez-vous en attente
     * @param PDO $pdo Instance de PDO
     * @param int $patientId ID du patient
     * @param int $formId ID du formulaire
     * @param string $priority Priorité du rendez-vous
     * @return string ID du dernier rendez-vous inséré
     */
    public static function createPending($pdo, $patientId, $formId, $priority)
    {
        // Insérer un nouveau rendez-vous avec le statut 'pending'
        $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, form_id, status, priority) VALUES (?, ?, 'pending', ?)");
        $stmt->execute([$patientId, $formId, $priority]);
        return $pdo->lastInsertId();
    }

    /**
     * Assigner un créneau à un rendez-vous
     * @param PDO $pdo Instance de PDO
     * @param int $slotId ID du créneau
     * @param int $formId ID du formulaire
     * @return void
     */
    public static function assignSlot($pdo, $slotId, $formId)
    {
        // Mettre à jour le rendez-vous pour assigner le créneau et confirmer le statut
        $stmt = $pdo->prepare("UPDATE appointments SET slot_id = ?, preferred_slot_id = ?, status = 'confirmed' WHERE form_id = ?");
        $stmt->execute([$slotId, $slotId, $formId]);
    }

    /**
     * Créer une notification de confirmation
     * @param PDO $pdo Instance de PDO
     * @param int $patientId ID du patient
     * @return void
     */
    public static function createConfirmationNotification($pdo, $patientId)
    {
        // Insérer une notification de confirmation pour le patient
        $stmt = $pdo->prepare("INSERT INTO notifications (patient_id, type, status) VALUES (?, 'confirmation', 'pending')");
        $stmt->execute([$patientId]);
    }

    /**
     * Signaler un rendez-vous
     * @param PDO $pdo Instance de PDO
     * @param int $appointmentId ID du rendez-vous
     * @param int $flaggedBy ID de l'utilisateur qui signale
     * @param string $comment Commentaire du signalement
     * @return void
     */
    public static function flagAppointment($pdo, $appointmentId, $flaggedBy, $comment)
    {
        // Insérer un signalement pour le rendez-vous
        $stmt = $pdo->prepare("INSERT INTO slot_flags (appointment_id, flagged_by, comment) VALUES (?, ?, ?)");
        $stmt->execute([$appointmentId, $flaggedBy, $comment]);
    }

    /**
     * Compter les signalements non résolus
     * @param PDO $pdo Instance de PDO
     * @return int Nombre de signalements non résolus
     */
    public static function countUnresolvedFlags($pdo)
    {
        // Compter les signalements non résolus
        $stmt = $pdo->query("SELECT COUNT(*) FROM slot_flags WHERE resolved = 0");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtenir les demandes de signalement non résolues
     * @param PDO $pdo Instance de PDO
     * @return array Liste des signalements non résolus avec détails
     */
    public static function getUnresolvedFlagRequests($pdo)
    {
        // Sélectionner les signalements non résolus avec informations sur le patient et le créneau
        $stmt = $pdo->query(
            "SELECT sf.*, a.id as appt_id, p.name, t.date, t.start_time, t.end_time
            FROM slot_flags sf
            JOIN appointments a ON sf.appointment_id = a.id
            JOIN patients p ON a.patient_id = p.id
            LEFT JOIN time_slots t ON a.slot_id = t.id
            WHERE sf.resolved = 0"
        );
        return $stmt->fetchAll();
    }

    /**
     * Obtenir les rendez-vous par date et statut
     * @param PDO $pdo Instance de PDO
     * @param string $dateFrom Date de début (optionnel)
     * @param string $dateTo Date de fin (optionnel)
     * @param string $status Statut du rendez-vous (optionnel)
     * @return array Liste des rendez-vous filtrés
     */
    public static function getAppointmentsByDateStatus($pdo, $dateFrom = '', $dateTo = '', $status = '')
    {
        // Construire la requête SQL de base pour sélectionner les rendez-vous
        $sql = "SELECT a.*, p.name, f.priority, f.routing, t.date, t.start_time, t.end_time
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            JOIN intake_forms f ON a.form_id = f.id
            LEFT JOIN time_slots t ON a.slot_id = t.id
            WHERE 1=1";
        $params = [];

        // Ajouter le filtre de statut si spécifié
        if ($status !== '' && $status !== 'all') {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }

        // Ajouter le filtre de date de début si spécifié
        if ($dateFrom !== '') {
            $sql .= " AND t.date >= ?";
            $params[] = $dateFrom;
        }

        // Ajouter le filtre de date de fin si spécifié
        if ($dateTo !== '') {
            $sql .= " AND t.date <= ?";
            $params[] = $dateTo;
        }

        // Ajouter l'ordre de tri
        $sql .= " ORDER BY t.date DESC, t.start_time DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get appointment counts grouped by status for the current week.
     *
     * @param PDO $pdo
     * @return array<int, array{status:string,count:int}>
     */
    public static function getWeeklyAppointmentStatusCounts($pdo)
    {
        $stmt = $pdo->prepare(
            "SELECT a.status, COUNT(*) AS count
            FROM appointments a
            JOIN time_slots t ON a.slot_id = t.id
            WHERE YEARWEEK(t.date, 1) = YEARWEEK(CURDATE(), 1)
            GROUP BY a.status"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get counts of auto-assigned vs receptionist-assigned cases.
     *
     * @param PDO $pdo
     * @return array<int, array{routing:string,count:int}>
     */
    public static function getAssignmentCountByRouting($pdo)
    {
        $stmt = $pdo->query(
            "SELECT f.routing, COUNT(*) AS count
            FROM appointments a
            JOIN intake_forms f ON a.form_id = f.id
            GROUP BY f.routing"
        );
        return $stmt->fetchAll();
    }

    /**
     * Get appointment counts grouped by priority level.
     *
     * @param PDO $pdo
     * @return array<int, array{priority:string,count:int}>
     */
    public static function getPriorityCounts($pdo)
    {
        $stmt = $pdo->query(
            "SELECT f.priority, COUNT(*) AS count
            FROM appointments a
            JOIN intake_forms f ON a.form_id = f.id
            GROUP BY f.priority"
        );
        return $stmt->fetchAll();
    }

    /**
     * Get unresolved slot flags with patient name, appointment date, doctor comment, and doctor name.
     *
     * @param PDO $pdo
     * @return array<int, array{flag_id:int, patient_name:string, date:string|null, comment:string, doctor_name:string}>
     */
    public static function getUnresolvedSlotFlagDetails($pdo)
    {
        $stmt = $pdo->query(
            "SELECT sf.id AS flag_id, p.name AS patient_name, t.date, sf.comment, u.name AS doctor_name
            FROM slot_flags sf
            JOIN appointments a ON sf.appointment_id = a.id
            JOIN patients p ON a.patient_id = p.id
            LEFT JOIN time_slots t ON a.slot_id = t.id
            JOIN users u ON sf.flagged_by = u.id
            WHERE sf.resolved = 0"
        );
        return $stmt->fetchAll();
    }
}
