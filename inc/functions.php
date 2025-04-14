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
    
    // Inclure le fichier de lecture sans distraction
    require_once get_template_directory() . '/inc/distraction-free.php';
    
    // Inclure les fichiers de fonctionnalités
    require_once get_template_directory() . '/inc/customizer.php';
    require_once get_template_directory() . '/inc/widgets.php';
    require_once get_template_directory() . '/inc/social-links.php';

    // Forcer la réinitialisation des règles de réécriture lors du chargement de la page d'administration
    function lejournaldesactus_force_flush_rewrite_rules() {
        global $pagenow;
        
        // Vérifier si les règles ont déjà été réinitialisées
        $flushed = get_option('lejournaldesactus_flush_rewrite');
        
        // Réinitialiser les règles de réécriture uniquement si ce n'est pas déjà fait
        if (is_admin() && ($pagenow == 'edit.php' || $pagenow == 'post-new.php') && !$flushed) {
            flush_rewrite_rules();
            update_option('lejournaldesactus_flush_rewrite', true);
            error_log('Règles de réécriture réinitialisées une seule fois.');
        }
    }
    add_action('admin_init', 'lejournaldesactus_force_flush_rewrite_rules');

    // Les fichiers liés au carrousel ont été supprimés
}
add_action('after_setup_theme', 'lejournaldesactus_include_files');
