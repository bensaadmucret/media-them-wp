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
        'title'    => __('Templates', 'lejournaldesactus'),
        'priority' => 30,
    ));
    
    // Option pour choisir le template des articles
    $wp_customize->add_setting('lejournaldesactus_single_post_template', array(
        'default'           => 'with-sidebar',
        'sanitize_callback' => 'lejournaldesactus_sanitize_select',
    ));
    
    $wp_customize->add_control('lejournaldesactus_single_post_template', array(
        'label'    => __('Template par défaut pour les articles', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_templates_section',
        'type'     => 'select',
        'choices'  => array(
            'with-sidebar'    => __('Avec sidebar', 'lejournaldesactus'),
            'without-sidebar' => __('Sans sidebar', 'lejournaldesactus'),
        ),
    ));
    
    // Option pour choisir le template des pages
    $wp_customize->add_setting('lejournaldesactus_page_template', array(
        'default'           => 'with-sidebar',
        'sanitize_callback' => 'lejournaldesactus_sanitize_select',
    ));
    
    $wp_customize->add_control('lejournaldesactus_page_template', array(
        'label'    => __('Template par défaut pour les pages', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_templates_section',
        'type'     => 'select',
        'choices'  => array(
            'with-sidebar'    => __('Avec sidebar', 'lejournaldesactus'),
            'without-sidebar' => __('Sans sidebar', 'lejournaldesactus'),
        ),
    ));
}
add_action('customize_register', 'lejournaldesactus_customize_register');

/**
 * Fonction de validation pour les options de type select
 */
function lejournaldesactus_sanitize_select($input, $setting) {
    // Les options valides
    $choices = $setting->manager->get_control($setting->id)->choices;
    
    // Retourne l'entrée si elle est valide ou la valeur par défaut
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}
