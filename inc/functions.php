<?php
/**
 * Fonctions utilitaires pour le thème Le Journal des Actus
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Inclure les fichiers nécessaires au fonctionnement du thème
 */
function lejournaldesactus_include_files() {
    // Inclure le fichier de gestion de la newsletter
    require_once get_template_directory() . '/inc/newsletter.php';
    
    // Inclure le fichier d'envoi de la newsletter
    require_once get_template_directory() . '/inc/newsletter-sender.php';
    
    // Inclure le fichier de gestion RGPD
    require_once get_template_directory() . '/inc/rgpd.php';
}
add_action('after_setup_theme', 'lejournaldesactus_include_files');
