<?php
/**
 * Fonctions de sécurité du thème
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Désactiver la divulgation de la version de WordPress
 */
function lejournaldesactus_remove_version() {
    return '';
}
add_filter('the_generator', 'lejournaldesactus_remove_version');

/**
 * Supprimer les balises meta inutiles
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

/**
 * Sécuriser les formulaires avec nonce
 */
function lejournaldesactus_add_nonce_to_forms() {
    wp_nonce_field('lejournaldesactus_form_action', 'lejournaldesactus_form_nonce');
}

/**
 * Vérifier le nonce des formulaires
 */
function lejournaldesactus_verify_nonce($nonce_field = 'lejournaldesactus_form_nonce', $action = 'lejournaldesactus_form_action') {
    if (!isset($_POST[$nonce_field]) || !wp_verify_nonce($_POST[$nonce_field], $action)) {
        die('Vérification de sécurité échouée. Veuillez réessayer.');
    }
    return true;
}

/**
 * Sécuriser les données des utilisateurs
 */
function lejournaldesactus_sanitize_data($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = lejournaldesactus_sanitize_data($value);
        }
    } else {
        // Supprimer les balises HTML et PHP
        $data = strip_tags($data);
        
        // Échapper les caractères spéciaux SQL
        $data = esc_sql($data);
        
        // Échapper les attributs HTML
        $data = esc_attr($data);
    }
    
    return $data;
}
