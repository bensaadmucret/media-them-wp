<?php
/**
 * Configuration de base du thème
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Configuration du thème
 */
function lejournaldesactus_setup() {
    // Ajouter la prise en charge du titre automatique
    add_theme_support('title-tag');
    
    // Ajouter la prise en charge des miniatures
    add_theme_support('post-thumbnails');
    
    // Ajouter la prise en charge des traductions
    load_theme_textdomain('lejournaldesactus', LEJOURNALDESACTUS_THEME_DIR . '/languages');
    
    // Définir le français comme langue par défaut si la locale n'est pas définie
    if (get_locale() == '') {
        update_option('WPLANG', 'fr_FR');
    }
    
    // Ajouter la prise en charge des menus
    register_nav_menus(array(
        'primary' => esc_html__('Menu Principal', 'lejournaldesactus'),
        'footer-useful' => esc_html__('Liens Utiles (Footer)', 'lejournaldesactus'),
        'footer-services' => esc_html__('Nos Services (Footer)', 'lejournaldesactus'),
        'footer-links1' => esc_html__('Liens Footer 1', 'lejournaldesactus'),
        'footer-links2' => esc_html__('Liens Footer 2', 'lejournaldesactus'),
    ));
    
    // Ajouter la prise en charge des formats de publication
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
}
add_action('after_setup_theme', 'lejournaldesactus_setup');

/**
 * Enregistrer les scripts et styles
 */
function lejournaldesactus_scripts() {
    // Vendor styles
    wp_enqueue_style('bootstrap', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap/css/bootstrap.min.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('bootstrap-icons', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('aos', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/aos/aos.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('swiper', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('glightbox', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/glightbox/css/glightbox.min.css', array(), LEJOURNALDESACTUS_VERSION);

    // Theme styles
    wp_enqueue_style('lejournaldesactus-style', LEJOURNALDESACTUS_THEME_URI . '/style.css', array('bootstrap'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-main', LEJOURNALDESACTUS_THEME_URI . '/assets/css/main.css', array('bootstrap'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-typography', LEJOURNALDESACTUS_THEME_URI . '/assets/css/typography.css', array('bootstrap'), LEJOURNALDESACTUS_VERSION);
    
    // Header and Menu styles
    wp_enqueue_style('lejournaldesactus-header-layout', LEJOURNALDESACTUS_THEME_URI . '/assets/css/header-layout.css', array('bootstrap', 'lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-mobile-menu', LEJOURNALDESACTUS_THEME_URI . '/assets/css/mobile-menu.css', array('bootstrap', 'lejournaldesactus-header-layout'), LEJOURNALDESACTUS_VERSION);
    
    // Other styles
    wp_enqueue_style('lejournaldesactus-post', LEJOURNALDESACTUS_THEME_URI . '/assets/css/post.css', array('bootstrap', 'lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-widgets', LEJOURNALDESACTUS_THEME_URI . '/assets/css/widgets.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-newsletter', LEJOURNALDESACTUS_THEME_URI . '/assets/css/newsletter.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-rgpd', LEJOURNALDESACTUS_THEME_URI . '/assets/css/rgpd.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-bookmarks', LEJOURNALDESACTUS_THEME_URI . '/assets/css/bookmarks.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/css/dark-mode.css', array(), LEJOURNALDESACTUS_VERSION);
    
    // Styles pour l'overlay du menu mobile en mode sombre
    wp_enqueue_style('lejournaldesactus-dark-mode-overlay', LEJOURNALDESACTUS_THEME_URI . '/assets/css/dark-mode-overlay.css', array('bootstrap', 'lejournaldesactus-main', 'lejournaldesactus-mobile-menu', 'lejournaldesactus-dark-mode'), LEJOURNALDESACTUS_VERSION);
    
    // Thème principal
    wp_enqueue_style('lejournaldesactus-theme', get_stylesheet_uri(), array('bootstrap', 'lejournaldesactus-main', 'lejournaldesactus-post'), LEJOURNALDESACTUS_VERSION);
    
    // Styles pour l'overlay du menu mobile en mode sombre - Priorité maximale
    wp_enqueue_style('lejournaldesactus-menu-overlay-dark', LEJOURNALDESACTUS_THEME_URI . '/assets/css/menu-overlay-dark.css', array('bootstrap', 'lejournaldesactus-main', 'lejournaldesactus-mobile-menu', 'lejournaldesactus-dark-mode', 'lejournaldesactus-dark-mode-overlay', 'lejournaldesactus-theme'), LEJOURNALDESACTUS_VERSION);
    
    // Scripts
    // Bibliothèques externes
    wp_enqueue_script('bootstrap', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('aos', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/aos/aos.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('swiper', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('glightbox', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/glightbox/js/glightbox.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    
    // Scripts du thème
    wp_enqueue_script('lejournaldesactus-main', LEJOURNALDESACTUS_THEME_URI . '/assets/js/main.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-purecounter', LEJOURNALDESACTUS_THEME_URI . '/assets/js/purecounter.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-newsletter', LEJOURNALDESACTUS_THEME_URI . '/assets/js/newsletter.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-rgpd', LEJOURNALDESACTUS_THEME_URI . '/assets/js/rgpd.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    
    // Scripts des nouvelles fonctionnalités
    wp_enqueue_script('lejournaldesactus-bookmarks', LEJOURNALDESACTUS_THEME_URI . '/assets/js/bookmarks.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/js/dark-mode.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-mobile-menu', LEJOURNALDESACTUS_THEME_URI . '/assets/js/mobile-menu.js', array('jquery', 'lejournaldesactus-dark-mode'), LEJOURNALDESACTUS_VERSION, true);
    
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'lejournaldesactus_scripts');

/**
 * Enregistrer les widgets
 */
function lejournaldesactus_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'lejournaldesactus'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Ajouter des widgets ici.', 'lejournaldesactus'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Widgets du footer - Colonne 1
    register_sidebar(array(
        'name'          => esc_html__('Footer Colonne 1', 'lejournaldesactus'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Widgets pour la première colonne du footer.', 'lejournaldesactus'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Widgets du footer - Colonne 2
    register_sidebar(array(
        'name'          => esc_html__('Footer Colonne 2', 'lejournaldesactus'),
        'id'            => 'footer-2',
        'description'   => esc_html__('Widgets pour la deuxième colonne du footer.', 'lejournaldesactus'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Widgets du footer - Colonne 3
    register_sidebar(array(
        'name'          => esc_html__('Footer Colonne 3', 'lejournaldesactus'),
        'id'            => 'footer-3',
        'description'   => esc_html__('Widgets pour la troisième colonne du footer.', 'lejournaldesactus'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Widgets du footer - Colonne 4
    register_sidebar(array(
        'name'          => esc_html__('Footer Colonne 4', 'lejournaldesactus'),
        'id'            => 'footer-4',
        'description'   => esc_html__('Widgets pour la quatrième colonne du footer.', 'lejournaldesactus'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'lejournaldesactus_widgets_init');
