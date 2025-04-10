<?php
/**
 * Le Journal des Actus functions and definitions
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

// Définir les constantes du thème
define('LEJOURNALDESACTUS_VERSION', '1.0.0');
define('LEJOURNALDESACTUS_THEME_DIR', get_template_directory());
define('LEJOURNALDESACTUS_THEME_URI', get_template_directory_uri());

// Inclure la classe WP_Bootstrap_Navwalker
require_once LEJOURNALDESACTUS_THEME_DIR . '/class-wp-bootstrap-navwalker.php';

// Inclure les fichiers de fonctionnalités
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/setup.php';           // Configuration de base du thème
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/security.php';        // Fonctions de sécurité
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/seo.php';             // Optimisation SEO
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/template-functions.php'; // Fonctions liées aux templates
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/custom-authors.php';  // Fonctions pour les auteurs personnalisés
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/customizer.php';      // Personnalisation du thème
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/maintenance-mode.php'; // Mode maintenance
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/related-posts.php';   // Articles liés intelligents
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/reading-time.php';    // Temps de lecture estimé
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/widgets.php';         // Widgets avancés
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/comments-control.php'; // Contrôle des commentaires
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/rgpd.php';            // Gestion RGPD
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/newsletter.php';      // Système de newsletter
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/trending-posts.php';  // Système d'articles tendance
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/bookmarks.php';       // Système de favoris/bookmarks
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/dark-mode.php';       // Système de mode sombre/clair
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/page-related-posts.php'; // Articles liés pour les pages
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/author-profile-link.php'; // Lien entre utilisateurs et profils d'auteurs

/**
 * Inclure les fonctionnalités d'administration pour la gestion des auteurs
 */
// require get_template_directory() . '/inc/admin-author.php'; // Désactivé car problèmes de performance

// Restaurer la métabox d'auteur standard de WordPress
function lejournaldesactus_restore_default_author_metabox() {
    // Supprimer explicitement la métabox personnalisée
    remove_meta_box('lejournaldesactus_author_meta_box', 'post', 'normal');
    remove_meta_box('lejournaldesactus_author_meta_box', 'post', 'side');
    remove_meta_box('lejournaldesactus_author_meta_box', 'post', 'advanced');
    
    // Restaurer la métabox d'auteur standard
    add_meta_box(
        'authordiv',
        __('Author'),
        'post_author_meta_box',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'lejournaldesactus_restore_default_author_metabox', 20);

// Inclure les fichiers de fonctions supplémentaires
require_once get_template_directory() . '/inc/functions.php';
