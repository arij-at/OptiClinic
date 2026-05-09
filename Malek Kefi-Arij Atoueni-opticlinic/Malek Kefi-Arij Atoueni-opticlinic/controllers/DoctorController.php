<?php
require_once __DIR__ . '/../models/AppointmentModel.php';

class DoctorController
{
    public static function dashboard($pdo)
    {
        // Vérifier l'autorisation de l'utilisateur
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
            header('Location: login.php');
            exit;
        }

        // Récupérer les rendez-vous confirmés pour aujourd'hui
        $appointments = AppointmentModel::getAppointmentsByDateStatus($pdo, date('Y-m-d'), date('Y-m-d'), 'confirmed');

        // Charger la vue du tableau de bord
        require __DIR__ . '/../views/doctor/dashboard.php';
    }
}
