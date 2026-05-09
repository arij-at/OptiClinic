<?php
require_once 'includes/init.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';
require_once 'controllers/PatientController.php';

// Vérification du token CSRF
csrf_verify();

// Traitement du formulaire du patient
PatientController::store($pdo);