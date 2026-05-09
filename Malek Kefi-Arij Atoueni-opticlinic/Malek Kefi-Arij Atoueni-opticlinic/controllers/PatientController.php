<?php
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/FormModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/TriageModel.php';

class PatientController
{
    public static function store($pdo)
    {
        // Récupération et sanitisation des données d'entrée
        $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
        $dob = htmlspecialchars(strip_tags(trim($_POST['date_of_birth'])));
        $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
        $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
        $symptoms = htmlspecialchars(strip_tags(trim($_POST['symptoms_description'] ?? '')));
        $answers = [
            'q_which_eye' => htmlspecialchars(strip_tags(trim($_POST['q_which_eye']))),
            'q_how_long' => htmlspecialchars(strip_tags(trim($_POST['q_how_long']))),
            'q_pain_level' => (int)$_POST['q_pain_level'],
            'q_redness' => (int)$_POST['q_redness'],
            'q_discharge' => (int)$_POST['q_discharge'],
            'q_vision' => (int)$_POST['q_vision'],
        ];

        // Calcul du score de triage
        $triage = TriageModel::score($answers);
        $token = bin2hex(random_bytes(32));

        // Création du patient
        $patientId = Patient::create($pdo, $name, $dob, $email, $phone, $token);
        // Création du formulaire d'admission
        $formId = FormModel::create(
            $pdo,
            $patientId,
            $symptoms,
            $answers['q_which_eye'],
            $answers['q_how_long'],
            $answers['q_pain_level'],
            $answers['q_redness'],
            $answers['q_discharge'],
            $answers['q_vision'],
            $triage['score'],
            $triage['priority'],
            $triage['is_ambiguous'],
            $triage['routing']
        );
        // Création du rendez-vous en attente
        AppointmentModel::createPending($pdo, $patientId, $formId, $triage['priority']);

        // Configuration de la session pour le patient
        $_SESSION['patient_id'] = $patientId;
        $_SESSION['token'] = $token;
        $_SESSION['role'] = 'patient'; // Explicitly set role for patient

        // Redirection selon le routage
        if ($triage['routing'] === 'receptionist') {
            header('Location: flagged.php');
        } else {
            header('Location: calendar.php');
        }
        exit;
    }
}
