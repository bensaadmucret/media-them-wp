<?php
/**
 * Fonctions de personnalisation du thème
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Personnalisation du thème - Customizer
 */
function lejournaldesactus_customize_register($wp_customize) {
    // Section pour les templates
    $wp_customize->add_section('lejournaldesactus_templates_section', array(
        'title'    => '📄 ' . __('Templates', 'lejournaldesactus'),
        'priority' => 30,
    ));
    
    // Option pour choisir le template des articles
    $wp_customize->add_setting('lejournaldesactus_single_post_template', array(
        'default'           => 'with-sidebar',
        'sanitize_callback' => 'lejournaldesactus_sanitize_select',
    ));
    
    $wp_customize->add_control('lejournaldesactus_single_post_template', array(
        'label'   => '📰 ' . __('Template des articles', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_templates_section',
        'type'    => 'select',
        'choices' => array(
            'with-sidebar'    => __('Avec barre latérale', 'lejournaldesactus'),
            'without-sidebar' => __('Sans barre latérale', 'lejournaldesactus'),
        ),
    ));
    
    // Option pour choisir le template des pages
    $wp_customize->add_setting('lejournaldesactus_page_template', array(
        'default'           => 'with-sidebar',
        'sanitize_callback' => 'lejournaldesactus_sanitize_select',
    ));
    
    $wp_customize->add_control('lejournaldesactus_page_template', array(
        'label'   => '📄 ' . __('Template des pages', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_templates_section',
        'type'    => 'select',
        'choices' => array(
            'with-sidebar'    => __('Avec barre latérale', 'lejournaldesactus'),
            'without-sidebar' => __('Sans barre latérale', 'lejournaldesactus'),
        ),
    ));

    // Section pour les modules optionnels
    $wp_customize->add_section('lejournaldesactus_modules_section', array(
        'title'    => '🔧 ' . __('Modules du thème', 'lejournaldesactus'),
        'priority' => 40,
    ));

    // Activer/désactiver le mode sombre
    $wp_customize->add_setting('lejournaldesactus_enable_dark_mode', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('lejournaldesactus_enable_dark_mode', array(
        'type'    => 'checkbox',
        'label'   => '🌑 ' . __('Activer le mode sombre', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_modules_section',
    ));

    // Activer/désactiver les favoris/bookmarks
    $wp_customize->add_setting('lejournaldesactus_enable_bookmarks', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('lejournaldesactus_enable_bookmarks', array(
        'type'    => 'checkbox',
        'label'   => '📚 ' . __('Activer les favoris/bookmarks', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_modules_section',
    ));

    // Activer/désactiver la newsletter
    $wp_customize->add_setting('lejournaldesactus_enable_newsletter', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('lejournaldesactus_enable_newsletter', array(
        'type'    => 'checkbox',
        'label'   => '📨 ' . __('Activer la newsletter', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_modules_section',
    ));

    // Activer/désactiver les articles tendance
    $wp_customize->add_setting('lejournaldesactus_enable_trending_posts', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('lejournaldesactus_enable_trending_posts', array(
        'type'    => 'checkbox',
        'label'   => '🔥 ' . __('Activer les articles tendance', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_modules_section',
    ));

    // Activer/désactiver le temps de lecture
    $wp_customize->add_setting('lejournaldesactus_enable_reading_time', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('lejournaldesactus_enable_reading_time', array(
        'type'    => 'checkbox',
        'label'   => '🕒 ' . __('Activer le temps de lecture', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_modules_section',
    ));

    // Activer/désactiver le menu landing page
    $wp_customize->add_setting('lejournaldesactus_enable_landing_menu', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('lejournaldesactus_enable_landing_menu', array(
        'type'    => 'checkbox',
        'label'   => '📋 ' . __('Activer le menu landing page', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_modules_section',
    ));
}
add_action('customize_register', 'lejournaldesactus_customize_register');

// Supprimer la section "colors" native du Customizer si elle ne sert plus
add_action('customize_register', function($wp_customize) {
    $wp_customize->remove_section('colors');
}, 20);

/**
 * Fonction de validation pour les options de type select
 */
function lejournaldesactus_sanitize_select($input, $setting) {
    // Les options valides
    $choices = $setting->manager->get_control($setting->id)->choices;
    
    // Retourne l'entrée si elle est valide ou la valeur par défaut
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}
