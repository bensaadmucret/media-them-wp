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
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/custom-menu-walker.php';

// Inclure les fichiers de fonctionnalités
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/setup.php';           // Configuration de base du thème
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/security.php';        // Fonctions de sécurité
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/seo.php';             // Optimisation SEO
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/template-functions.php'; // Fonctions liées aux templates
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/template-tags.php'; 
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/custom-header.php'; 
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/custom-authors.php';  // Fonctions pour les auteurs personnalisés
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/customizer.php';      // Personnalisation du thème

if (get_theme_mod('lejournaldesactus_enable_dark_mode', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/dark-mode.php';
}
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/css-loader.php';      // Chargeur CSS centralisé
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/admin-functions.php'; // Nouvelles fonctions d'administration
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/maintenance-mode.php'; // Mode maintenance
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/related-posts.php';   // Articles liés intelligents
if (get_theme_mod('lejournaldesactus_enable_reading_time', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/reading-time.php';    // Temps de lecture estimé
}
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/widgets.php';         // Widgets avancés
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/comments-control.php'; // Contrôle des commentaires
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/rgpd.php';            // Gestion RGPD
if (get_theme_mod('lejournaldesactus_enable_newsletter', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/newsletter.php';      // Système de newsletter
}
if (get_theme_mod('lejournaldesactus_enable_trending_posts', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/trending-posts.php';  // Système d'articles tendance
}
if (get_theme_mod('lejournaldesactus_enable_bookmarks', true)) {
    require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/bookmarks.php';       // Système de favoris/bookmarks
}
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/page-related-posts.php'; // Articles liés pour les pages
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/author-profile-link.php'; // Lien entre utilisateurs et profils d'auteurs
require_once LEJOURNALDESACTUS_THEME_DIR . '/inc/login-customizer.php'; // Personnalisation de la page de connexion


// =========================
// 1. Customizer Section Design
// =========================
add_action('customize_register', function($wp_customize) {
    // Section Design améliorée
    $wp_customize->add_section('lejournaldesactus_design', array(
        'title'    => '🎨 ' . __('Design du site', 'lejournaldesactus'),
        'priority' => 30,
        'description' => __('Personnalisez l’apparence générale de votre site en temps réel. Astuce : combinez couleurs, polices et arrondis pour un look unique !','lejournaldesactus'),
    ));

    // Couleurs principales
    $wp_customize->add_setting('lejournaldesactus_primary_color', array(
        'default'   => '#f75815',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lejournaldesactus_primary_color', array(
        'label'       => '🎨 ' . __('Couleur principale', 'lejournaldesactus'),
        'description' => __('Utilisée pour les boutons, liens actifs et éléments interactifs.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_design',
        'settings'    => 'lejournaldesactus_primary_color',
    )));

    $wp_customize->add_setting('lejournaldesactus_secondary_color', array(
        'default'   => '#A2F8B5',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lejournaldesactus_secondary_color', array(
        'label'       => '🌱 ' . __('Couleur secondaire', 'lejournaldesactus'),
        'description' => __('Accent sur certains boutons, liens ou backgrounds.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_design',
        'settings'    => 'lejournaldesactus_secondary_color',
    )));

    $wp_customize->add_setting('lejournaldesactus_bg_color', array(
        'default'   => '#ffffff',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lejournaldesactus_bg_color', array(
        'label'       => '🖼️ ' . __('Couleur de fond', 'lejournaldesactus'),
        'description' => __('Arrière-plan principal du site.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_design',
        'settings'    => 'lejournaldesactus_bg_color',
    )));

    $wp_customize->add_setting('lejournaldesactus_text_color', array(
        'default'   => '#212529',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lejournaldesactus_text_color', array(
        'label'       => '📝 ' . __('Couleur du texte', 'lejournaldesactus'),
        'description' => __('Couleur principale du texte.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_design',
        'settings'    => 'lejournaldesactus_text_color',
    )));

    // Police principale (Bunny Fonts)
    $wp_customize->add_setting('lejournaldesactus_font_family', array(
        'default'   => 'Inter',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('lejournaldesactus_font_family', array(
        'label'       => '🅰️ ' . __('Police principale (Bunny Fonts)', 'lejournaldesactus'),
        'description' => __('Police utilisée pour tout le texte du site. Exemples visibles en live à droite.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_design',
        'settings'    => 'lejournaldesactus_font_family',
        'type'        => 'select',
        'choices'     => array(
            'Inter'      => 'Inter',
            'Open Sans'  => 'Open Sans',
            'Lato'       => 'Lato',
            'Montserrat' => 'Montserrat',
            'Roboto'     => 'Roboto',
            'Nunito'     => 'Nunito',
            'Poppins'    => 'Poppins',
            'Raleway'    => 'Raleway',
            'Merriweather' => 'Merriweather',
            'Space Grotesk' => 'Space Grotesk',
            'Bricolage Grotesque' => 'Bricolage Grotesque',
        ),
    ));

    // Taille de police de base
    $wp_customize->add_setting('lejournaldesactus_font_size', array(
        'default'   => 16,
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('lejournaldesactus_font_size', array(
        'label'       => '🔠 ' . __('Taille du texte (px)', 'lejournaldesactus'),
        'description' => __('Taille de base du texte (corps de page).', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_design',
        'settings'    => 'lejournaldesactus_font_size',
        'type'        => 'number',
        'input_attrs' => array('min' => 12, 'max' => 24),
    ));

    // Arrondi global
    $wp_customize->add_setting('lejournaldesactus_border_radius', array(
        'default'   => 6,
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('lejournaldesactus_border_radius', array(
        'label'       => '⬜ ' . __('Arrondi global (px)', 'lejournaldesactus'),
        'description' => __('Arrondi des boutons, champs, blocs…', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_design',
        'settings'    => 'lejournaldesactus_border_radius',
        'type'        => 'number',
        'input_attrs' => array('min' => 0, 'max' => 32),
    ));
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
    if (isset($bunny_fonts[$font])) {
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

// Enqueue du script du builder dans le Customizer uniquement
// (SUPPRIMÉ)

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
require get_template_directory() . '/inc/featured-image-credit.php';

// Inclure la configuration spécifique au site si elle existe
if (file_exists(get_template_directory() . '/site-specific.php')) {
    require_once get_template_directory() . '/site-specific.php';
}

// Inclure le générateur de sitemap
require_once get_template_directory() . '/inc/sitemap.php';

// Inclure les outils SEO supplémentaires (robots.txt, etc.)
require_once get_template_directory() . '/inc/seo-tools.php';

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
