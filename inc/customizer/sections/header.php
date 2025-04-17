<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Section Header (personnalisation avancée)
Kirki::add_section( 'lejournaldesactus_header', [
    'title'    => esc_html__( 'En-tête du site', 'lejournaldesactus' ),
    'panel'    => 'lejournaldesactus_appearance',
    'priority' => 5,
] );

// Affichage/masquage de la barre d’alerte
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_header_show_alert',
    'label'    => esc_html__( 'Afficher une barre d’alerte', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_header',
    'default'  => false,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
] );

// Texte de la barre d’alerte
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_header_alert_text',
    'label'    => esc_html__( 'Texte de la barre d’alerte', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_header',
    'default'  => '',
    'active_callback' => [
        [
            'setting'  => 'lejournaldesactus_header_show_alert',
            'operator' => '==',
            'value'    => true,
        ],
    ],
] );

// Logo alternatif pour le mode sombre
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'image',
    'settings' => 'lejournaldesactus_header_logo_dark',
    'label'    => esc_html__( 'Logo pour le mode sombre', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_header',
    'default'  => '',
    'description' => esc_html__( 'Sera affiché si le mode sombre est actif.', 'lejournaldesactus' ),
] );

// Texte ou slogan sous le logo
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_header_slogan',
    'label'    => esc_html__( 'Slogan sous le logo', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_header',
    'default'  => '',
    'description' => esc_html__( 'Texte affiché sous le logo principal.', 'lejournaldesactus' ),
] );

// --- AJOUTS PANEL HEADER ---
// Affichage/masquage de la barre de recherche
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_header_show_search',
    'label'    => esc_html__( 'Afficher la barre de recherche', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_header',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
] );

// Choix de la position du menu
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'radio-buttonset',
    'settings' => 'lejournaldesactus_header_menu_position',
    'label'    => esc_html__( 'Position du menu', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_header',
    'default'  => 'center',
    'choices'  => [
        'left'   => esc_html__( 'Gauche', 'lejournaldesactus' ),
        'center' => esc_html__( 'Centre', 'lejournaldesactus' ),
        'right'  => esc_html__( 'Droite', 'lejournaldesactus' ),
    ],
] );

// Hauteur du header ajustable
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'        => 'slider',
    'settings'    => 'lejournaldesactus_header_height',
    'label'       => esc_html__( 'Hauteur du header (px)', 'lejournaldesactus' ),
    'section'     => 'lejournaldesactus_header',
    'default'     => 120,
    'choices'     => [
        'min'  => 60,
        'max'  => 300,
        'step' => 1,
    ],
] );
