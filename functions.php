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

// Inclure la classe Custom_Menu_Walker pour le menu mobile
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/menu/custom-menu-walker.php';

// Inclure les fichiers de fonctionnalités
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/setup.php';           // Configuration de base du thème
// require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/security.php';        // Fonctions de sécurité (ancien, supprimé car le fichier n'existe plus)
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/seo/seo.php';             // Optimisation SEO (corrigé, déplacé dans inc/seo/)
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/template-functions.php'; // Fonctions liées aux templates
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/template-tags.php'; 
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/custom-header.php'; 
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/authors/custom-authors.php';  // Fonctions pour les auteurs personnalisés (corrigé, déplacé dans inc/authors/)
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/customizer/appearance.php';
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/customizer/templates.php';
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/css-loader.php';      // Chargeur CSS centralisé
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/admin/admin-functions.php'; // Nouvelles fonctions d'administration
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/maintenance-mode.php'; // Mode maintenance
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/post/related-posts.php';   // Articles liés intelligents (corrigé, déplacé dans inc/post/)
if (get_theme_mod('lejournaldesactus_enable_reading_time', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/post/reading-time.php';    // Temps de lecture estimé (chemin corrigé)
}
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/post/comments-control.php'; // Contrôle des commentaires
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/security/rgpd.php';            // Gestion RGPD
if (get_theme_mod('lejournaldesactus_enable_newsletter', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/process-newsletter/newsletter.php';      // Système de newsletter
}
if (get_theme_mod('lejournaldesactus_enable_trending_posts', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/post/trending-posts.php';  // Système d'articles tendance
}
if (get_theme_mod('lejournaldesactus_enable_bookmarks', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/post/bookmarks.php';       // Système de favoris/bookmarks
}
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/post/page-related-posts.php'; // Articles liés pour les pages
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/authors/author-profile-link.php'; // Lien entre utilisateurs et profils d'auteurs
// Charger Kirki embarqué si le plugin n'est pas actif
if ( ! class_exists( 'Kirki' ) ) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/kirki/kirki.php';
}
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/customizer/login-customizer.php'; // Personnalisation de la page de connexion

// Ajout dynamique de classes body selon la mise en page
add_filter('body_class', function($classes) {
    $site_width = get_theme_mod('lejournaldesactus_site_width', 'full');
    if ($site_width === 'boxed') {
        $classes[] = 'site-boxed';
    } else {
        $classes[] = 'site-fullwidth';
    }
    return $classes;
});

// Désactiver toutes les notices admin de Kirki
add_filter( 'kirki_show_notice', '__return_false' );

// Masquer les notices spécifiques de Kirki via CSS au cas où
add_action( 'admin_enqueue_scripts', function() {
    echo '<style>
        .kirki-admin-notice,
        .kirki-discount-notice {
            display: none !important;
        }
    </style>';
});

// Inclusion automatique des modules organisés par familles
foreach (glob(get_template_directory() . '/inc/customizer/*.php') as $file) {
    // On évite de charger deux fois la config Kirki
    if (basename($file) !== 'kirki-config.php') {
        require_once $file;
    }
}
foreach (glob(get_template_directory() . '/inc/seo/*.php') as $file) {
    require_once $file;
}
foreach (glob(get_template_directory() . '/inc/newsletter/*.php') as $file) {
    require_once $file;
}
foreach (glob(get_template_directory() . '/inc/widgets/*.php') as $file) {
    require_once $file;
}
foreach (glob(get_template_directory() . '/inc/security/*.php') as $file) {
    require_once $file;
}
foreach (glob(get_template_directory() . '/inc/admin/*.php') as $file) {
    require_once $file;
}
foreach (glob(get_template_directory() . '/inc/social/*.php') as $file) {
    require_once $file;
}

// Enregistrement du bloc Gutenberg Carrousel
add_action('init', function() {
    register_block_type( get_template_directory() . '/blocks/carousel' );
});

// Charger Swiper uniquement si le bloc carousel est présent dans le contenu
add_action('wp_enqueue_scripts', function() {
    if (is_admin()) return;
    global $post;
    if (empty($post)) return;
    if (has_block('lejournaldesactus/carousel', $post)) {
        wp_enqueue_style('swiper', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.css', array(), LEJOURNALDESACTUS_VERSION);
        wp_enqueue_script('swiper', LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/swiper/swiper-bundle.min.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
        wp_enqueue_script(
            'lejournaldesactu-carousel-frontend',
            get_template_directory_uri() . '/blocks/carousel/frontend.js',
            array('swiper'),
            filemtime(get_template_directory() . '/blocks/carousel/frontend.js'),
            true
        );
    }
});

// S'assurer que le script frontend du bloc dépend de Swiper
add_filter(
    'block_type_metadata_settings',
    function( $settings, $block_type ) {
        if ( is_object($block_type) && isset($block_type->name) && $block_type->name === 'lejournaldesactus/carousel' ) {
            if ( isset( $settings['script'] ) ) {
                if ( is_array( $settings['script'] ) && isset( $settings['script']['deps'] ) ) {
                    $settings['script']['deps'][] = 'swiper';
                }
            }
        }
        return $settings;
    },
    10,
    2
);

// Enqueue testimonials block assets (editor + front)
add_action('enqueue_block_assets', function() {
    // Path to built JS and CSS
    $dir = get_template_directory_uri() . '/blocks/testimonials/build';
    $asset_file = get_template_directory() . '/blocks/testimonials/build/index.asset.php';
    if (file_exists($asset_file)) {
        $asset = include($asset_file);
        wp_enqueue_script(
            'lejournaldesactus-testimonials',
            $dir . '/index.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );
        wp_enqueue_style(
            'lejournaldesactus-testimonials',
            $dir . '/style-index.css',
            array(),
            $asset['version']
        );
    }
});

// Enqueue FAQ block assets (editor + front)
add_action('enqueue_block_assets', function() {
    $dir = get_template_directory_uri() . '/blocks/faq/build';
    $asset_file = get_template_directory() . '/blocks/faq/build/index.asset.php';
    if (file_exists($asset_file)) {
        $asset = include($asset_file);
        wp_enqueue_script(
            'lejournaldesactus-faq',
            $dir . '/index.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );
        wp_enqueue_style(
            'lejournaldesactus-faq',
            $dir . '/style-index.css',
            array(),
            $asset['version']
        );
        // Enqueue le JS d'accordéon pour le front uniquement
        if (!is_admin()) {
            wp_enqueue_script(
                'lejournaldesactus-faq-frontend',
                get_template_directory_uri() . '/blocks/faq/frontend.js',
                array(),
                $asset['version'],
                true
            );
        }
    }
});

// Enqueue accessibility toggles JS 
add_action('wp_enqueue_scripts', function() {
    // JS pour les toggles accessibilité
    wp_enqueue_script(
        'lejournaldesactus-accessibility-toggles',
        get_template_directory_uri() . '/assets/accessibility-toggles.js',
        array(),
        filemtime(get_template_directory() . '/assets/accessibility-toggles.js'),
        true
    );
});

// Enqueue le JS accessibilité (boutons + / -)
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('lejda-accessibility', get_template_directory_uri() . '/assets/js/accessibility.js', [], null, true);
});

// Passer l'option mode sombre auto au JS et enqueue le script
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('lejda-darkmode-auto', get_template_directory_uri() . '/assets/js/darkmode-auto.js', [], null, true);
    $enabled = get_theme_mod('lejournaldesactus_blog_darkmode_auto', false) ? 'true' : 'false';
    wp_add_inline_script('lejda-darkmode-auto', 'window.lejda_darkmode_auto = ' . $enabled . ';');
});

// Enqueue le CSS des badges
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('lejda-badges', get_template_directory_uri() . '/assets/css/badges.css', [], null);
});

// =========================
// 2. Génération dynamique des variables CSS Customizer
// =========================
add_action('wp_head', function() {
    $primary   = get_theme_mod('lejournaldesactus_primary_color', '#f75815');
    $secondary = get_theme_mod('lejournaldesactus_secondary_color', '#A2F8B5');
    $bg        = get_theme_mod('lejournaldesactus_bg_color', '#ffffff');
    $text      = get_theme_mod('lejournaldesactus_text_color', '#212529');
    $font      = get_theme_mod('lejournaldesactus_font_family', 'Inter');
    $font_size = get_theme_mod('lejournaldesactus_font_size', 16);
    $radius    = get_theme_mod('lejournaldesactus_border_radius', 6);
    echo '<style id="lejournaldesactus-customizer-vars">
    :root {
        --primary-color: ' . esc_attr($primary) . ';
        --secondary-color: ' . esc_attr($secondary) . ';
        --body-bg: ' . esc_attr($bg) . ';
        --text-color: ' . esc_attr($text) . ';
        --font-family-base: ' . esc_attr($font) . ', sans-serif;
        --font-size-base: ' . intval($font_size) . 'px;
        --border-radius-base: ' . intval($radius) . 'px;
    }
    body { font-family: var(--font-family-base); font-size: var(--font-size-base); background: var(--body-bg); color: var(--text-color); }
    .btn, button, input, textarea, select { border-radius: var(--border-radius-base); }
    </style>';
});

// =========================
// 3. Charger dynamiquement la police Bunny Fonts choisie
// =========================
add_action('wp_head', function() {
    $font = get_theme_mod('lejournaldesactus_font_family', 'Inter');
    // Correspondances Bunny Fonts (nom => slug)
    $bunny_fonts = array(
        'Inter' => 'inter',
        'Open Sans' => 'open-sans',
        'Lato' => 'lato',
        'Montserrat' => 'montserrat',
        'Roboto' => 'roboto',
        'Nunito' => 'nunito',
        'Poppins' => 'poppins',
        'Raleway' => 'raleway',
        'Merriweather' => 'merriweather',
        'Space Grotesk' => 'space-grotesk',
        'Bricolage Grotesque' => 'bricolage-grotesque',
    );
    // Correction robustesse : s'assurer que $font est une string reconnue
    if ( is_string($font) && isset($bunny_fonts[$font]) ) {
        $slug = $bunny_fonts[$font];
        // Poids courants pour le body + titres
        $weights = '400;500;700';
        echo '<link rel="stylesheet" href="https://fonts.bunny.net/css?family=' . esc_attr($slug) . ':' . $weights . '" />';
    }
}, 5); // Avant le bloc de variables CSS

// =========================
// 4. Enqueue du JS Customizer pour live preview ET panneau
// =========================
add_action('customize_preview_init', function() {
    wp_enqueue_script(
        'lejournaldesactus-customizer',
        get_template_directory_uri() . '/assets/js/customizer.js',
        array('jquery', 'customize-preview'),
        filemtime(get_template_directory() . '/assets/js/customizer.js'),
        true
    );
});
add_action('customize_controls_enqueue_scripts', function() {
    wp_enqueue_script(
        'lejournaldesactus-customizer',
        get_template_directory_uri() . '/assets/js/customizer.js',
        array('jquery'),
        filemtime(get_template_directory() . '/assets/js/customizer.js'),
        true
    );
});

// =========================
// 5. Enqueue du CSS Customizer admin pour un look premium
// =========================
add_action('customize_controls_enqueue_scripts', function() {
    wp_enqueue_style(
        'lejournaldesactus-admin-customizer',
        get_template_directory_uri() . '/assets/css/admin-customizer.css',
        array(),
        filemtime(get_template_directory() . '/assets/css/admin-customizer.css')
    );
});

// Appliquer la taille de police globale et le mode contraste élevé depuis le Customizer
add_action('wp_head', function() {
    $fontsize = get_theme_mod('lejournaldesactus_accessibility_fontsize', 18);
    $contrast = get_theme_mod('lejournaldesactus_accessibility_high_contrast', false);
    echo '<style type="text/css">body { font-size: ' . intval($fontsize) . 'px; }</style>';
    if ($contrast) {
        echo '<style type="text/css">body.high-contrast, .high-contrast { background:#000!important;color:#fff!important; }
        a, a:visited { color: #ff0 !important; text-decoration: underline !important; }
        .header, .footer, .main-content { background: #111 !important; color: #fff !important; }
        button, input, select, textarea { background: #000 !important; color: #fff !important; border:2px solid #fff !important; }
        </style>';
    }
});

// Ajouter la classe high-contrast sur body si activé
add_filter('body_class', function($classes) {
    if (get_theme_mod('lejournaldesactus_accessibility_high_contrast', false)) {
        $classes[] = 'high-contrast';
    }
    return $classes;
});

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

// Inclure le fichier de gestion du crédit photo
require get_template_directory() . '/inc/post/featured-image-credit.php';

// Inclure la configuration spécifique au site si elle existe
if (file_exists(get_template_directory() . '/site-specific.php')) {
    require_once get_template_directory() . '/site-specific.php';
}

// Inclure le générateur de sitemap
require_once get_template_directory() . '/inc/seo/sitemap.php';

// Inclure les outils SEO supplémentaires (robots.txt, etc.)
require_once get_template_directory() . '/inc/seo/seo-tools.php';

// Inclure les widgets personnalisés
require_once get_template_directory() . '/inc/widgets/selected-pages-widget.php';

// Enqueue du style moderne pour les commentaires
add_action('wp_enqueue_scripts', function() {
    if (is_singular() && (comments_open() || get_comments_number())) {
        wp_enqueue_style(
            'lejournaldesactus-comments',
            get_template_directory_uri() . '/assets/css/comments.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/comments.css')
        );
    }
});
