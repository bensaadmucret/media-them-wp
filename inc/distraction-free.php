<?php
/**
 * Fonctionnalités de lecture sans distraction
 * 
 * Ce fichier contient toutes les fonctions nécessaires pour
 * implémenter le mode de lecture sans distraction sur le thème.
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Enregistre les scripts et styles nécessaires pour le mode lecture sans distraction
 */
function lejournaldesactus_register_distraction_free_assets() {
    // N'enregistrer les assets que sur les pages d'articles individuels
    if (!is_single()) {
        return;
    }
    
    // Enregistrer et charger le CSS
    wp_enqueue_style(
        'lejournaldesactus-distraction-free',
        get_template_directory_uri() . '/assets/css/distraction-free.css',
        array(),
        filemtime(get_template_directory() . '/assets/css/distraction-free.css')
    );
    
    // Enregistrer et charger le JavaScript
    wp_enqueue_script(
        'lejournaldesactus-distraction-free',
        get_template_directory_uri() . '/assets/js/distraction-free.js',
        array(),
        filemtime(get_template_directory() . '/assets/js/distraction-free.js'),
        true
    );
    
    // Ajouter le script pour la barre de progression de lecture
    wp_add_inline_script('lejournaldesactus-distraction-free', '
        document.addEventListener("DOMContentLoaded", function() {
            const progressBar = document.querySelector(".reading-progress-bar");
            const article = document.querySelector(".blog-details-content");
            
            if (!progressBar || !article) return;
            
            window.addEventListener("scroll", function() {
                const articleHeight = article.offsetHeight;
                const windowHeight = window.innerHeight;
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const articleTop = article.offsetTop;
                
                // Calculer la position relative de défilement dans l\'article
                let scrollPercentage = 0;
                
                if (scrollTop > articleTop) {
                    const scrollPosition = scrollTop - articleTop;
                    const scrollableArea = articleHeight - windowHeight;
                    
                    if (scrollableArea > 0) {
                        scrollPercentage = (scrollPosition / scrollableArea) * 100;
                        scrollPercentage = Math.min(100, Math.max(0, scrollPercentage));
                    }
                }
                
                // Mettre à jour la barre de progression
                progressBar.style.width = scrollPercentage + "%";
            });
        });
    ');
}
add_action('wp_enqueue_scripts', 'lejournaldesactus_register_distraction_free_assets');

/**
 * Calcule le temps de lecture estimé d'un article
 * 
 * @return int Temps de lecture en minutes
 */
function lejournaldesactus_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    
    // Vitesse de lecture moyenne : 200 mots par minute
    $reading_time = ceil($word_count / 200);
    
    // Minimum 1 minute
    return max(1, $reading_time);
}

/**
 * Ajoute des options de personnalisation pour le mode lecture sans distraction
 * 
 * @param WP_Customize_Manager $wp_customize Objet Customizer
 */
function lejournaldesactus_distraction_free_customizer($wp_customize) {
    // Ajouter une section pour les options de lecture sans distraction
    $wp_customize->add_section('lejournaldesactus_distraction_free_section', array(
        'title'       => __('Mode Lecture Zen', 'lejournaldesactus'),
        'description' => __('Personnalisez les options du mode de lecture sans distraction.', 'lejournaldesactus'),
        'priority'    => 160,
    ));
    
    // Option pour activer/désactiver la fonctionnalité
    $wp_customize->add_setting('lejournaldesactus_enable_distraction_free', array(
        'default'           => true,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_enable_distraction_free', array(
        'label'    => __('Activer le mode lecture zen', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_distraction_free_section',
        'type'     => 'checkbox',
    ));
    
    // Option pour la taille de police en mode lecture sans distraction
    $wp_customize->add_setting('lejournaldesactus_distraction_free_font_size', array(
        'default'           => '1.1',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('lejournaldesactus_distraction_free_font_size', array(
        'label'    => __('Taille de police (rem)', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_distraction_free_section',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 0.8,
            'max'  => 2,
            'step' => 0.1,
        ),
    ));
    
    // Option pour la couleur de fond en mode lecture sans distraction
    $wp_customize->add_setting('lejournaldesactus_distraction_free_bg_color', array(
        'default'           => '#f8f9fa',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lejournaldesactus_distraction_free_bg_color', array(
        'label'    => __('Couleur de fond', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_distraction_free_section',
    )));
}
add_action('customize_register', 'lejournaldesactus_distraction_free_customizer');

// La fonction lejournaldesactus_sanitize_checkbox est déjà définie dans maintenance-mode.php
// Nous ne la redéclarons pas ici pour éviter les erreurs

/**
 * Ajoute des styles personnalisés basés sur les options du Customizer
 */
function lejournaldesactus_distraction_free_custom_css() {
    if (!is_single()) {
        return;
    }
    
    $font_size = get_theme_mod('lejournaldesactus_distraction_free_font_size', '1.1');
    $bg_color = get_theme_mod('lejournaldesactus_distraction_free_bg_color', '#f8f9fa');
    
    $custom_css = "
        body.distraction-free-mode {
            background-color: {$bg_color};
        }
        .df-content {
            font-size: {$font_size}rem;
        }
    ";
    
    wp_add_inline_style('lejournaldesactus-distraction-free', $custom_css);
}
add_action('wp_enqueue_scripts', 'lejournaldesactus_distraction_free_custom_css', 20);

/**
 * Conditionnellement affiche le bouton de lecture sans distraction
 * basé sur les paramètres du Customizer
 */
function lejournaldesactus_maybe_disable_distraction_free() {
    if (!is_single()) {
        return;
    }
    
    $enabled = get_theme_mod('lejournaldesactus_enable_distraction_free', true);
    
    if (!$enabled) {
        wp_add_inline_style('lejournaldesactus-distraction-free', '
            .distraction-free-toggle {
                display: none !important;
            }
        ');
    }
}
add_action('wp_enqueue_scripts', 'lejournaldesactus_maybe_disable_distraction_free', 30);
