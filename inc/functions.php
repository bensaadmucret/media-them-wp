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
    require_once get_template_directory() . '/inc/process-newsletter/newsletter.php';
    
    // Inclure le fichier d'envoi de la newsletter
    require_once get_template_directory() . '/inc/process-newsletter/newsletter-sender.php';
    
    // Inclure le fichier de gestion RGPD
    require_once get_template_directory() . '/inc/security/rgpd.php';
    
    // Inclure le fichier de lecture sans distraction
    require_once get_template_directory() . '/inc/post/distraction-free.php';
    
    // Inclure les fichiers de fonctionnalités
    // require_once get_template_directory() . '/inc/customizer.php'; // Fichier inexistant, inclusion désactivée
    // require_once get_template_directory() . '/inc/widgets.php'; // Fichier inexistant, inclusion désactivée (widgets gérés ailleurs)
    // require_once get_template_directory() . '/inc/social-links.php'; // Fichier inexistant, inclusion désactivée (liens sociaux gérés ailleurs)

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

/**
 * Retourne les choix disponibles pour le champ "orderby" dans les widgets LJDA
 */
function ljda_get_orderby_query_choices() {
    return array(
        'date'       => esc_html__('Date (récent d’abord)', 'lejournaldesactus'),
        'modified'   => esc_html__('Date de modification', 'lejournaldesactus'),
        'title'      => esc_html__('Titre', 'lejournaldesactus'),
        'author'     => esc_html__('Auteur', 'lejournaldesactus'),
        'comment_count' => esc_html__('Nombre de commentaires', 'lejournaldesactus'),
        'rand'       => esc_html__('Aléatoire', 'lejournaldesactus'),
    );
}

/**
 * Retourne les tailles d’image disponibles pour les widgets LJDA
 */
function ljda_get_image_sizes() {
    global $_wp_additional_image_sizes;
    $sizes = array(
        'thumbnail' => esc_html__('Miniature', 'lejournaldesactus'),
        'medium'    => esc_html__('Moyenne', 'lejournaldesactus'),
        'large'     => esc_html__('Grande', 'lejournaldesactus'),
        'full'      => esc_html__('Taille originale', 'lejournaldesactus'),
    );
    if ( isset($_wp_additional_image_sizes['ljda-860x570']) ) {
        $sizes['ljda-860x570'] = esc_html__('Grille Magazine (860x570)', 'lejournaldesactus');
    }
    return $sizes;
}

/**
 * Retourne les choix de termes pour une taxonomie donnée (ex: catégories, étiquettes)
 */
function ljda_get_taxonomy_term_choices($taxonomy = 'category') {
    $terms = get_terms(array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ));
    $choices = array('' => esc_html__('Toutes', 'lejournaldesactus'));
    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            $choices[$term->term_id] = $term->name;
        }
    }
    return $choices;
}
