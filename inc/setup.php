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
        'elementor-landing' => esc_html__('Menu Landing Elementor', 'lejournaldesactus'),
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
    // Vendor styles
    wp_enqueue_style('bootstrap', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap/css/bootstrap.min.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('bootstrap-icons', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.css', array(), LEJOURNALDESACTUS_VERSION);
    wp_enqueue_style('aos', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/aos/aos.css', array(), LEJOURNALDESACTUS_VERSION);
    // wp_enqueue_style('swiper', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.css', array(), LEJOURNALDESACTUS_VERSION); // Désormais chargé conditionnellement
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
    // wp_enqueue_script('swiper', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true); // Désormais chargé conditionnellement
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

// Fallback pour la fonction is_dark_mode_active() si le module dark mode est désactivé
if (!function_exists('is_dark_mode_active')) {
    function is_dark_mode_active() {
        return false;
    }
}

// Handler global pour afficher les erreurs de commentaire en toast/pop
add_filter('wp_die_handler', function($handler) {
    return function($message, $title = '', $args = array()) {
        // Si l'erreur concerne les commentaires, on redirige avec le message dans l'URL
        if (isset($_SERVER['HTTP_REFERER']) && (
            (is_string($message) && stripos($message, 'comment') !== false) ||
            (is_string($title) && stripos($title, 'comment') !== false)
        )) {
            $ref = $_SERVER['HTTP_REFERER'];
            $msg = urlencode(strip_tags(is_array($message) ? implode(' ', $message) : $message));
            wp_safe_redirect(add_query_arg('comment_error_msg', $msg, $ref));
            exit;
        }
        // Sinon comportement normal
        _default_wp_die_handler($message, $title, $args);
    };
});

// --- Anti-spam commentaires : Honeypot & Anti-flood ---
add_filter('comment_form_defaults', function($defaults) {
    $defaults['fields']['lejournaldesactus_hp'] = '<p style="display:none !important;"><label>Ne pas remplir : <input type="text" name="lejournaldesactus_hp" value="" autocomplete="off"></label></p>';
    $defaults['fields']['lejournaldesactus_ts'] = '<input type="hidden" name="lejournaldesactus_ts" value="' . time() . '">';
    return $defaults;
});

add_filter('preprocess_comment', function($commentdata) {
    // Honeypot : si le champ caché est rempli, on bloque
    if (!empty($_POST['lejournaldesactus_hp'])) {
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url();
        wp_safe_redirect(add_query_arg('comment_error', 'honeypot', $ref));
        exit;
    }
    // Anti-flood : délai minimal (5s) entre affichage et soumission
    if (isset($_POST['lejournaldesactus_ts'])) {
        $min_delay = 5; // secondes
        $now = time();
        $elapsed = $now - intval($_POST['lejournaldesactus_ts']);
        if ($elapsed < $min_delay) {
            $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url();
            wp_safe_redirect(add_query_arg('comment_error', 'flood', $ref));
            exit;
        }
    }
    return $commentdata;
}, 20);

add_filter('comment_form_defaults', function($defaults) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $a = rand(1, 9);
        $b = rand(1, 9);
        $question = "$a + $b = ?";
        $expected = $a + $b;
        $defaults['fields']['lejournaldesactus_captcha'] = '<p class="lejournaldesactus-captcha-field"><label for="lejournaldesactus_captcha">Question anti-spam : <span class="lejournaldesactus-captcha-question" style="font-weight:bold">' . $question . '</span> <input type="text" name="lejournaldesactus_captcha" id="lejournaldesactus_captcha" size="2" maxlength="2" required autocomplete="off">' . '<input type="hidden" name="lejournaldesactus_captcha_expected" value="' . $expected . '"></label></p>';
    }
    return $defaults;
});

add_filter('preprocess_comment', function($commentdata) {
    // On n'utilise plus la session, mais le champ caché
    $expected = isset($_POST['lejournaldesactus_captcha_expected']) ? intval($_POST['lejournaldesactus_captcha_expected']) : null;
    $provided = isset($_POST['lejournaldesactus_captcha']) ? intval($_POST['lejournaldesactus_captcha']) : null;
    if ($expected === null || $provided !== $expected) {
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url();
        wp_safe_redirect(add_query_arg('comment_error', 'captcha', $ref));
        exit;
    }
    return $commentdata;
});

/**
 * Helper : Générer dynamiquement le header/footer (1 ligne, 1 à 3 colonnes)
 */
function lejournaldesactus_render_builder_zone($zone = 'header') {
    $data = get_theme_mod('lejournaldesactus_header_footer_builder_data', '');
    if (!$data) return;
    $structure = json_decode($data, true);
    // Migration auto ancienne structure (sécurité)
    if (is_array($structure) && isset($structure[$zone]) && !isset($structure[$zone]['columns'])) {
        // Ancienne structure : tableau de blocs → tout dans la 1ère colonne
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
