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
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    // Le chargement des traductions doit être fait sur after_setup_theme
    // donc on déplace load_theme_textdomain ici
    // (déjà correct car c'est bien dans la fonction de hook)
    load_theme_textdomain('lejournaldesactus', LEJOURNALDESACTUS_THEME_DIR . '/languages');
    if (get_locale() == '') {
        update_option('WPLANG', 'fr_FR');
    }
    register_nav_menus(array(
        'primary' => esc_html__('Menu Principal', 'lejournaldesactus'),
        'footer-useful' => esc_html__('Liens Utiles (Footer)', 'lejournaldesactus'),
        'footer-services' => esc_html__('Nos Services (Footer)', 'lejournaldesactus'),
        'footer-links1' => esc_html__('Liens Footer 1', 'lejournaldesactus'),
        'footer-links2' => esc_html__('Liens Footer 2', 'lejournaldesactus'),
        'elementor-landing' => esc_html__('Menu Landing Elementor', 'lejournaldesactus'),
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
}
// Correction : s'assurer que le hook est bien en place
add_action('after_setup_theme', 'lejournaldesactus_setup');

/**
 * Déclarer la compatibilité avec Elementor
 */
function lejournaldesactus_elementor_support() {
    add_theme_support('elementor');
}
add_action('after_setup_theme', 'lejournaldesactus_elementor_support');

/**
 * Enregistrer les scripts et styles
 */
function lejournaldesactus_scripts() {
    wp_enqueue_style('bootstrap', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap/css/bootstrap.min.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('bootstrap-icons', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('aos', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/aos/aos.css', array(), LEJOURNALDESACTUS_VERSION);
    // wp_enqueue_style('glightbox', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/glightbox/css/glightbox.min.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-style', LEJOURNALDESACTUS_THEME_URI . '/style.css', array('bootstrap'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-main', LEJOURNALDESACTUS_THEME_URI . '/assets/css/main.css', array('bootstrap'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-typography', LEJOURNALDESACTUS_THEME_URI . '/assets/css/typography.css', array('bootstrap'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-header-layout', LEJOURNALDESACTUS_THEME_URI . '/assets/css/header-layout.css', array('bootstrap', 'lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-mobile-menu', LEJOURNALDESACTUS_THEME_URI . '/assets/css/mobile-menu.css', array('bootstrap', 'lejournaldesactus-header-layout'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-post', LEJOURNALDESACTUS_THEME_URI . '/assets/css/post.css', array('bootstrap', 'lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-widgets', LEJOURNALDESACTUS_THEME_URI . '/assets/css/widgets.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-newsletter', LEJOURNALDESACTUS_THEME_URI . '/assets/css/newsletter.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-rgpd', LEJOURNALDESACTUS_THEME_URI . '/assets/css/rgpd.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-bookmarks', LEJOURNALDESACTUS_THEME_URI . '/assets/css/bookmarks.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/css/dark-mode.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-dark-mode-overlay', LEJOURNALDESACTUS_THEME_URI . '/assets/css/dark-mode-overlay.css', array('bootstrap', 'lejournaldesactus-main', 'lejournaldesactus-mobile-menu', 'lejournaldesactus-dark-mode'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-theme', get_stylesheet_uri(), array('bootstrap', 'lejournaldesactus-main', 'lejournaldesactus-post'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('lejournaldesactus-menu-overlay-dark', LEJOURNALDESACTUS_THEME_URI . '/assets/css/menu-overlay-dark.css', array('bootstrap', 'lejournaldesactus-main', 'lejournaldesactus-mobile-menu', 'lejournaldesactus-dark-mode', 'lejournaldesactus-dark-mode-overlay', 'lejournaldesactus-theme'), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_script('bootstrap', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('aos', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/aos/aos.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    // wp_enqueue_script('glightbox', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/glightbox/js/glightbox.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-purecounter', LEJOURNALDESACTUS_THEME_URI . '/assets/js/purecounter.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-main', LEJOURNALDESACTUS_THEME_URI . '/assets/js/main.js', array('jquery', 'lejournaldesactus-purecounter'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-newsletter', LEJOURNALDESACTUS_THEME_URI . '/assets/js/newsletter.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-rgpd', LEJOURNALDESACTUS_THEME_URI . '/assets/js/rgpd.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-bookmarks', LEJOURNALDESACTUS_THEME_URI . '/assets/js/bookmarks.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    wp_enqueue_script('lejournaldesactus-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/js/dark-mode.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
    if (!is_admin() && !is_customize_preview()) {
        wp_enqueue_script('lejournaldesactus-mobile-menu', LEJOURNALDESACTUS_THEME_URI . '/assets/js/mobile-menu.js', array('jquery', 'lejournaldesactus-dark-mode'), LEJOURNALDESACTUS_VERSION, true);
    }
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    if (is_singular() && (comments_open() || get_comments_number())) {
        wp_enqueue_script(
            'lejournaldesactus-comments',
            get_template_directory_uri() . '/assets/js/comments.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/comments.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'lejournaldesactus_scripts');

if (!function_exists('is_dark_mode_active')) {
    function is_dark_mode_active() {
        return false;
    }
}

add_filter('wp_die_handler', function($handler) {
    return function($message, $title = '', $args = array()) {
        if (isset($_SERVER['HTTP_REFERER']) && (
            (is_string($message) && stripos($message, 'comment') !== false) ||
            (is_string($title) && stripos($title, 'comment') !== false)
        )) {
            $ref = $_SERVER['HTTP_REFERER'];
            $msg = urlencode(strip_tags(is_array($message) ? implode(' ', $message) : $message));
            wp_safe_redirect(add_query_arg('comment_error_msg', $msg, $ref));
            exit;
        }
        _default_wp_die_handler($message, $title, $args);
    };
});

require_once get_template_directory() . '/inc/menu/ajax-menus.php';
require_once get_template_directory() . '/inc/menu/custom-menu-walker.php';
require_once get_template_directory() . '/inc/post/related-posts.php';
require_once get_template_directory() . '/inc/post/trending-posts.php';
require_once get_template_directory() . '/inc/post/page-related-posts.php';
require_once get_template_directory() . '/inc/post/reading-time.php';
require_once get_template_directory() . '/inc/post/comments-control.php';
require_once get_template_directory() . '/inc/post/bookmarks.php';
require_once get_template_directory() . '/inc/post/distraction-free.php';
require_once get_template_directory() . '/inc/post/featured-image-credit.php';
require_once get_template_directory() . '/inc/post/dark-mode.php';
require_once get_template_directory() . '/inc/admin/admin-functions.php';
require_once get_template_directory() . '/inc/process-newsletter/newsletter.php';
require_once get_template_directory() . '/inc/widgets/advanced-widgets.php';
require_once get_template_directory() . '/inc/customizer/login-customizer.php';
require_once get_template_directory() . '/inc/customizer/appearance.php';
require_once get_template_directory() . '/inc/customizer/templates.php';
require_once get_template_directory() . '/inc/customizer/kirki-config.php';

// --- Ajout : Forcer l'inclusion du CSS dynamique Kirki pour les variables CSS Customizer ---
add_action('wp_head', function() {
    if ( class_exists('Kirki') && method_exists('Kirki', 'print_styles') ) {
        Kirki::print_styles();
    }
}, 21);

function lejournaldesactus_render_builder_zone($zone = 'header') {
    $data = get_theme_mod('lejournaldesactus_header_footer_builder_data', '');
    if (!$data) return;
    $structure = json_decode($data, true);
    if (is_array($structure) && isset($structure[$zone]) && !isset($structure[$zone]['columns'])) {
        $structure[$zone] = array('columns' => [ is_array($structure[$zone]) ? $structure[$zone] : [] ]);
    }
    if (!is_array($structure) || !isset($structure[$zone]['columns']) || !is_array($structure[$zone]['columns'])) return;
    echo '<div class="builder-row">';
    foreach ($structure[$zone]['columns'] as $col) {
        echo '<div class="builder-col">';
        if (is_array($col)) {
            foreach ($col as $block) {
                switch ($block['type']) {
                    case 'logo':
                        echo '<div class="builder-block builder-logo">';
                        if (has_custom_logo()) {
                            the_custom_logo();
                        } else {
                            echo '<a href="'.esc_url(home_url('/')).'" class="site-title">'.get_bloginfo('name').'</a>';
                        }
                        echo '</div>';
                        break;
                    case 'menu':
                        echo '<div class="builder-block builder-menu">';
                        $menu_slug = isset($block['menu']) ? $block['menu'] : '';
                        if ($menu_slug) {
                            wp_nav_menu(array('menu' => $menu_slug, 'container' => false, 'menu_class' => 'builder-menu-list'));
                        } else {
                            echo '<span class="builder-menu-placeholder">Définir un menu</span>';
                        }
                        echo '</div>';
                        break;
                    case 'button':
                        echo '<div class="builder-block builder-button">';
                        $text = isset($block['text']) ? esc_html($block['text']) : 'Bouton';
                        $url = isset($block['url']) ? esc_url($block['url']) : '#';
                        echo '<a href="'.$url.'" class="builder-btn">'.$text.'</a>';
                        echo '</div>';
                        break;
                    case 'social':
                        echo '<div class="builder-block builder-social">';
                        $links = isset($block['links']) ? explode(',', $block['links']) : [];
                        foreach ($links as $link) {
                            $link = trim($link);
                            if ($link)
                                echo '<a href="'.esc_url($link).'" target="_blank" rel="noopener" class="builder-social-link">🌐</a> ';
                        }
                        echo '</div>';
                        break;
                    case 'cta':
                        echo '<div class="builder-block builder-cta">';
                        $text = isset($block['text']) ? esc_html($block['text']) : 'Call to Action';
                        $url = isset($block['url']) ? esc_url($block['url']) : '#';
                        echo '<a href="'.$url.'" class="builder-cta-btn">'.$text.'</a>';
                        echo '</div>';
                        break;
                }
            }
        }
        echo '</div>';
    }
    echo '</div>';
}

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
    register_sidebar(array(
        'name'          => esc_html__('Footer Colonne 1', 'lejournaldesactus'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Widgets pour la première colonne du footer.', 'lejournaldesactus'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    register_sidebar(array(
        'name'          => esc_html__('Footer Colonne 2', 'lejournaldesactus'),
        'id'            => 'footer-2',
        'description'   => esc_html__('Widgets pour la deuxième colonne du footer.', 'lejournaldesactus'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
    register_sidebar(array(
        'name'          => esc_html__('Footer Colonne 3', 'lejournaldesactus'),
        'id'            => 'footer-3',
        'description'   => esc_html__('Widgets pour la troisième colonne du footer.', 'lejournaldesactus'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
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
