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
    
    // Ajouter la prise en charge de l'en-tête HTTP strict-transport-security
    add_filter('wp_headers', 'lejournaldesactus_add_security_headers');
}
add_action('after_setup_theme', 'lejournaldesactus_setup');

/**
 * Enregistrer les scripts et styles
 */
function lejournaldesactus_scripts() {
    // Styles
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', array(), null);
    
    wp_enqueue_style('bootstrap', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap/css/bootstrap.min.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('bootstrap-icons', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('aos', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/aos/aos.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('swiper', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('glightbox', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/glightbox/css/glightbox.min.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-style', LEJOURNALDESACTUS_THEME_URI . '/assets/css/main.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-widgets', LEJOURNALDESACTUS_THEME_URI . '/assets/css/widgets.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-theme', get_stylesheet_uri(), array(), LEJOURNALDESACTUS_VERSION);
    
    // Scripts
    wp_enqueue_script('bootstrap', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js', array(), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('aos', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/aos/aos.js', array(), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('swiper', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.js', array(), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('glightbox', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/glightbox/js/glightbox.min.js', array(), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-main', LEJOURNALDESACTUS_THEME_URI . '/assets/js/main.js', array(), LEJOURNALDESACTUS_VERSION, true);
    
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

/**
 * Ajouter les en-têtes de sécurité
 */
function lejournaldesactus_security_headers() {
    // Content Security Policy
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: *.googleapis.com *.gstatic.com *.bootstrapcdn.com *.cloudflare.com; " .
           "style-src 'self' 'unsafe-inline' *.googleapis.com *.gstatic.com *.bootstrapcdn.com *.cloudflare.com; " .
           "img-src 'self' data: *.gravatar.com *.wp.com *.wordpress.com; " .
           "font-src 'self' data: *.googleapis.com *.gstatic.com *.bootstrapcdn.com *.cloudflare.com; " .
           "connect-src 'self'; " .
           "worker-src 'self' blob:; " .
           "frame-src 'self'; " .
           "object-src 'none';";
    
    header("Content-Security-Policy: $csp");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}
add_action('send_headers', 'lejournaldesactus_security_headers');

/**
 * Ajouter le CSS pour corriger le menu mobile
 */
function lejournaldesactus_add_mobile_menu_fix() {
    // Utiliser LEJOURNALDESACTUS_THEME_URI au lieu de get_template_directory_uri() pour la cohérence
    wp_enqueue_style('lejournaldesactus-mobile-menu-fix', LEJOURNALDESACTUS_THEME_URI . '/assets/css/mobile-menu-fix.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_script('lejournaldesactus-mobile-menu', LEJOURNALDESACTUS_THEME_URI . '/assets/js/mobile-menu.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-purecounter', LEJOURNALDESACTUS_THEME_URI . '/assets/js/purecounter.min.js', array(), LEJOURNALDESACTUS_VERSION, true);
}
add_action('wp_enqueue_scripts', 'lejournaldesactus_add_mobile_menu_fix');
