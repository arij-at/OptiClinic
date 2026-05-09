<?php
require_once __DIR__ . '/../models/FormModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';

class ReceptionistController
{
    public static function dashboard($pdo)
    {
        // Vérifier l'autorisation de l'utilisateur
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
            header('Location: login.php');
            exit;
        }

        // Gestion de l'assignation de créneau
        if (isset($_GET['assign'])) {
            $_SESSION['assigning_form_id'] = (int)$_GET['assign'];
            header('Location: dashboard.php');
            exit;
        }

        // Annulation de l'assignation
        if (isset($_GET['cancel_assign'])) {
            unset($_SESSION['assigning_form_id']);
            header('Location: dashboard.php');
            exit;
        }

        // Récupération du formulaire en cours d'assignation
        $assigning_form = null;
        if (isset($_SESSION['assigning_form_id'])) {
            $assigning_form = FormModel::getFormWithPatientName($pdo, $_SESSION['assigning_form_id']);
        }

        // Récupération des filtres de recherche
        $searchName = trim($_GET['search_name'] ?? '');
        $filterPriority = $_GET['filter_priority'] ?? '';
        $filterRouting = $_GET['filter_routing'] ?? '';

        $apptDateFrom = $_GET['appt_date_from'] ?? '';
        $apptDateTo = $_GET['appt_date_to'] ?? '';
        $apptStatus = $_GET['appt_status'] ?? 'all';

        // Comptage des notifications
        $pending_forms = FormModel::countPendingReceptionistForms($pdo);
        $unresolved_flags = AppointmentModel::countUnresolvedFlags($pdo);
        $total_notifications = $pending_forms + $unresolved_flags;
        // Récupération des soumissions en attente
        $pending_submissions = FormModel::getPendingReceptionistSubmissions($pdo, $searchName, $filterPriority, $filterRouting);
        // Récupération des demandes des médecins
        $doctor_requests = AppointmentModel::getUnresolvedFlagRequests($pdo);
        // Récupération des rendez-vous confirmés avec filtres
        $confirmed_appointments = AppointmentModel::getAppointmentsByDateStatus($pdo, $apptDateFrom, $apptDateTo, $apptStatus);

        // Charger la vue du tableau de bord
        require __DIR__ . '/../views/receptionist/dashboard.php';
    }
}
