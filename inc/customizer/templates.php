<?php
/**
 * Customizer : Templates (structure, choix des templates)
 */
if (!defined('ABSPATH')) exit;

// Désactivation totale du Customizer natif pour les templates : tout passe par Kirki désormais.
// (Ancien code commenté)

// add_action('customize_register', 'lejournaldesactus_customize_register_templates');
// function lejournaldesactus_customize_register_templates($wp_customize) {
//     // Section pour les templates
//     // $wp_customize->add_section('lejournaldesactus_templates_section', array(
//     //     'title'    => '📄 ' . __('Templates', 'lejournaldesactus'),
//     //     'priority' => 30,
//     // ));
//     // Option pour choisir le template des articles
//     // $wp_customize->add_setting('lejournaldesactus_single_post_template', array(
//     //     'default'           => 'with-sidebar',
//     //     'sanitize_callback' => 'lejournaldesactus_sanitize_select',
//     // ));
//     // $wp_customize->add_control('lejournaldesactus_single_post_template', array(
//     //     'label'    => __('Template des articles', 'lejournaldesactus'),
//     //     'section'  => 'lejournaldesactus_templates_section',
//     //     'settings' => 'lejournaldesactus_single_post_template',
//     //     'type'     => 'radio',
//     //     'choices'  => array(
//     //         'with-sidebar' => __('Avec sidebar', 'lejournaldesactus'),
//     //         'full-width'   => __('Pleine largeur', 'lejournaldesactus'),
//     //     ),
//     // ));
// }

// (Ce fichier peut servir pour des hooks ou fonctions utilitaires non liés à l'UI Customizer)
