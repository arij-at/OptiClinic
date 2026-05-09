<?php
/**
 * lang.php — Chargeur de langue unique
 *
 * Charge le fichier de traduction correspondant à la langue active en session.
 * Toutes les chaînes de traduction sont définies dans lang/fr.php et lang/ar.php.
 * Utiliser la fonction t($key) pour récupérer une traduction.
 *
 * Langues supportées : 'fr' (défaut), 'ar'
 */

// Langues disponibles dans l'application
$supported_languages = ['fr', 'ar'];

// Langue active : récupérée de la session, 'fr' par défaut
$current_lang = $_SESSION['lang'] ?? 'fr';

// Sécurité : vérifier que la langue est bien supportée (évite les path traversal)
if (!in_array($current_lang, $supported_languages, true)) {
    $current_lang = 'fr';
}

// Chargement du fichier de langue
require_once __DIR__ . '/../lang/' . $current_lang . '.php';
// Le fichier chargé définit le tableau global $lang_strings
// Exemple d'accès : $lang_strings['submit'] → 'Soumettre' (fr) ou 'إرسال' (ar)
?>