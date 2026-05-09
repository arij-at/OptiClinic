<?php
class FormModel
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new intake form record
     * @param PDO $pdo
     * @param int $patientId
     * @param string $symptomsDescription
     * @param string $whichEye
     * @param string $howLong
     * @param int $painLevel
     * @param int $redness
     * @param int $discharge
     * @param int $vision
     * @param int $triageScore
     * @param string $priority
     * @param bool $isAmbiguous
     * @param string $routing
     * @return int
     */
    public static function create($pdo, $patientId, $symptomsDescription, $whichEye, $howLong, $painLevel, $redness, $discharge, $vision, $triageScore, $priority, $isAmbiguous, $routing)
    {
        // Insert intake form data into database
        $stmt = $pdo->prepare(
            "INSERT INTO intake_forms (patient_id, symptoms_description, q_which_eye, q_how_long, q_pain_level, q_redness, q_discharge, q_vision, triage_score, priority, is_ambiguous, routing) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        ); // Insert intake form record
        $stmt->execute([$patientId, $symptomsDescription, $whichEye, $howLong, $painLevel, $redness, $discharge, $vision, $triageScore, $priority, $isAmbiguous ? 1 : 0, $routing]);
        return $pdo->lastInsertId();
    }

    /**
     * Get form data with patient name by form ID
     * @param PDO $pdo
     * @param int $formId
     * @return array|null
     */
    public static function getFormWithPatientName($pdo, $formId)
    {
        // Retrieve form and patient data by form ID
        $stmt = $pdo->prepare("SELECT f.*, p.name FROM intake_forms f JOIN patients p ON f.patient_id = p.id WHERE f.id = ?"); // Select form with patient name
        $stmt->execute([$formId]);
        return $stmt->fetch();
    }

    /**
     * Count pending forms routed to receptionist
     * @param PDO $pdo
     * @return int
     */
    public static function countPendingReceptionistForms($pdo)
    {
        // Count pending appointments for receptionist routing
        $stmt = $pdo->query("SELECT COUNT(*) FROM intake_forms f JOIN appointments a ON f.id = a.form_id WHERE f.routing = 'receptionist' AND a.status = 'pending'"); // Count receptionist pending forms
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get pending submissions for receptionist with optional filters
     * @param PDO $pdo
     * @param string $searchName
     * @param string $priority
     * @param string $routing
     * @return array
     */
    public static function getPendingReceptionistSubmissions($pdo, $searchName = '', $priority = '', $routing = '')
    {
        // Build query for pending receptionist submissions with filters
        $sql = "SELECT f.*, p.name, p.email, p.phone, a.preferred_slot_id, t.date, t.start_time, t.end_time
            FROM intake_forms f
            JOIN patients p ON f.patient_id = p.id
            LEFT JOIN appointments a ON f.id = a.form_id
            LEFT JOIN time_slots t ON a.preferred_slot_id = t.id
            WHERE a.status = 'pending'"; // Base query for pending submissions
        $params = [];

        if ($routing === 'auto' || $routing === 'receptionist') {
            $sql .= " AND f.routing = ?";
            $params[] = $routing;
        } else {
            $sql .= " AND f.routing = 'receptionist'";
        }

        if ($searchName !== '') {
            $sql .= " AND p.name LIKE ?";
            $params[] = '%' . $searchName . '%';
        }

        if (in_array($priority, ['P1', 'P2', 'P3'], true)) {
            $sql .= " AND f.priority = ?";
            $params[] = $priority;
        }

        $sql .= " ORDER BY t.date DESC, t.start_time DESC";
        $stmt = $pdo->prepare($sql); // Prepare dynamic query
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get patient ID by form ID
     * @param PDO $pdo
     * @param int $formId
     * @return int|null
     */
    public static function getPatientIdByFormId($pdo, $formId)
    {
        // Retrieve patient ID associated with form
        $stmt = $pdo->prepare("SELECT patient_id FROM intake_forms WHERE id = ?"); // Select patient ID by form ID
        $stmt->execute([$formId]);
        return $stmt->fetchColumn();
    }

    /**
     * Get preferred slot ID by form ID
     * @param PDO $pdo
     * @param int $formId
     * @return int|null
     */
    public static function getPreferredSlotIdByFormId($pdo, $formId)
    {
        // Retrieve preferred slot ID for the form's appointment
        $stmt = $pdo->prepare("SELECT preferred_slot_id FROM appointments WHERE form_id = ?"); // Select preferred slot ID by form ID
        $stmt->execute([$formId]);
        return $stmt->fetchColumn();
    }
}
