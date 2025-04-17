<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Section Accessibilité
Kirki::add_section( 'lejournaldesactus_accessibility', [
    'title'    => esc_html__( 'Accessibilité', 'lejournaldesactus' ),
    'panel'    => 'lejournaldesactus_appearance',
    'priority' => 20,
] );

// Activer le bouton Police Dyslexique dans le header
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_accessibility_dyslexia_btn',
    'label'    => esc_html__( 'Afficher le bouton Police Dyslexique', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_accessibility',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
] );

// Activer le mode contraste élevé
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_accessibility_high_contrast',
    'label'    => esc_html__( 'Activer le mode contraste élevé', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_accessibility',
    'default'  => false,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
] );

// Afficher le bouton d’ajustement de la taille du texte
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_accessibility_fontsize_btn',
    'label'    => esc_html__( 'Afficher le bouton Taille du texte', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_accessibility',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
] );

// Taille de police globale par défaut
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'slider',
    'settings' => 'lejournaldesactus_accessibility_fontsize',
    'label'    => esc_html__( 'Taille de police globale (px)', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_accessibility',
    'default'  => 18,
    'choices'  => [
        'min'  => 14,
        'max'  => 24,
        'step' => 1,
    ],
    'description' => esc_html__( 'Taille de police par défaut pour le corps du site.', 'lejournaldesactus' ),
] );
