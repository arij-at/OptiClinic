<?php
/**
 * Modèle pour le triage des patients en fonction des réponses au questionnaire
 */
class TriageModel
{
    /**
     * Calculer le score de triage, déterminer la priorité et le routage
     * @param array $answers Réponses du questionnaire
     * @return array Tableau contenant le score, la priorité, le routage et l'ambiguïté
     */
    public static function score($answers)
    {
        // Calculer les scores individuels basés sur les réponses
        // Score de douleur : multiplié par 2.5 pour donner plus de poids aux douleurs élevées
        $pain_score = $answers['q_pain_level'] * 2.5;
        // Score de rougeur : multiplié par 2.5 pour pondérer l'inflammation
        $redness_score = $answers['q_redness'] * 2.5;
        // Score d'écoulement : multiplié par 2 pour pondérer les sécrétions
        $discharge_score = $answers['q_discharge'] * 2;
        // Score de vision : multiplié par 2.5 pour donner du poids aux problèmes visuels
        $vision_score = $answers['q_vision'] * 2.5;

        // Calculer le score total moyen pour normaliser sur une échelle commune
        $score = (int)(($pain_score + $redness_score + $discharge_score + $vision_score) / 4);

        // Initialiser la priorité par défaut à P3 (priorité basse)
        $priority = 'P3';
        // Règle de priorité P1 : si le score total >= 20, indique une urgence nécessitant une attention immédiate, déclenche P1
        if ($score >= 20) {
            $priority = 'P1';
        // Règle de priorité P2 : si le score total >= 12, indique une urgence modérée, déclenche P2
        } elseif ($score >= 12) {
            $priority = 'P2';
        }

        // Initialiser le routage par défaut à 'auto' (assignation automatique)
        $routing = 'auto';
        // Initialiser l'ambiguïté à false
        $isAmbiguous = false;

        // Règle d'urgence pour douleur élevée : si douleur >= 3, indique une douleur sévère nécessitant vérification humaine, déclenche P1 et routage réceptionniste
        if ($answers['q_pain_level'] >= 3 || $answers['q_vision'] >= 3) {
            $priority = 'P1';
            $routing = 'receptionist';
        }

        // Règle d'urgence pour symptômes récents : si les symptômes sont d'aujourd'hui et douleur/vision >= 2, indique une aggravation rapide, déclenche P1 et routage réceptionniste
        if ($answers['q_how_long'] === 'today' && ($answers['q_pain_level'] >= 2 || $answers['q_vision'] >= 2)) {
            $priority = 'P1';
            $routing = 'receptionist';
        }

        // Règle d'ambiguïté : si pas de douleur mais vision >= 3, cas ambigu nécessitant évaluation humaine, marque comme ambigu et routage réceptionniste
        if ($answers['q_pain_level'] == 0 && $answers['q_vision'] >= 3) {
            $isAmbiguous = true;
            $routing = 'receptionist';
        }

        // Règle finale de routage : si priorité P1 ou ambigu, forcer le routage réceptionniste pour vérification
        if ($priority === 'P1' || $isAmbiguous) {
            $routing = 'receptionist';
        }

        // Retourner les résultats du triage
        return [
            'score' => $score,
            'priority' => $priority,
            'routing' => $routing,
            'is_ambiguous' => $isAmbiguous
        ];
    }
}
