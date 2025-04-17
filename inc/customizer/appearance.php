<?php
/**
 * Customizer : Apparence (couleurs, polices, styles)
 */
if (!defined('ABSPATH')) exit;

// Réactivation du Customizer natif pour l'apparence
add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('lejournaldesactus_design', array(
        'title'    => ' ' . __('Design du site', 'lejournaldesactus'),
        'priority' => 30,
    ));
    $wp_customize->add_setting('lejournaldesactus_primary_color', array(
        'default'   => '#f75815',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'lejournaldesactus_primary_color', array(
        'label'   => __('Couleur principale', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_design',
        'settings'=> 'lejournaldesactus_primary_color',
    )));
    $wp_customize->add_setting('lejournaldesactus_breadcrumb_enable', array(
        'default'   => true,
        'transport' => 'refresh',
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control('lejournaldesactus_breadcrumb_enable', array(
        'type'    => 'checkbox',
        'label'   => __('Afficher le fil d\'Ariane (breadcrumb)', 'lejournaldesactus'),
        'section' => 'lejournaldesactus_design',
    ));
    // Ajoute ici d'autres réglages design si besoin
});

// (Ce fichier peut servir pour des hooks ou fonctions utilitaires non liés à l'UI Customizer)
