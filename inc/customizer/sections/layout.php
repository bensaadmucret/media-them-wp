<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Section Mise en page
Kirki::add_section( 'lejournaldesactus_layout', [
    'title'    => esc_html__( 'Mise en page', 'lejournaldesactus' ),
    'panel'    => 'lejournaldesactus_appearance',
    'priority' => 15,
] );

// Largeur du site (boxed/full width)
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'radio-image',
    'settings' => 'lejournaldesactus_site_width',
    'label'    => esc_html__( 'Largeur du site', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_layout',
    'default'  => 'full',
    'choices'  => [
        'full'  => get_template_directory_uri() . '/assets/img/fullwidth.svg',
        'boxed' => get_template_directory_uri() . '/assets/img/boxed.svg',
    ],
    'labels' => [
        'full'  => esc_html__( 'Pleine largeur', 'lejournaldesactus' ),
        'boxed' => esc_html__( 'Encadré (boxed)', 'lejournaldesactus' ),
    ],
] );

// Mode d'affichage des archives (grille ou liste)
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'radio-buttonset',
    'settings' => 'lejournaldesactus_archive_layout',
    'label'    => esc_html__( 'Affichage des archives', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_layout',
    'default'  => 'grid',
    'choices'  => [
        'grid' => esc_html__( 'Grille', 'lejournaldesactus' ),
        'list' => esc_html__( 'Liste', 'lejournaldesactus' ),
    ],
] );

// Nombre de colonnes pour la page d’accueil
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'slider',
    'settings' => 'lejournaldesactus_home_columns',
    'label'    => esc_html__( 'Nombre de colonnes (Accueil)', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_layout',
    'default'  => 3,
    'choices'  => [
        'min'  => 1,
        'max'  => 4,
        'step' => 1,
    ],
    'description' => esc_html__( 'Nombre de colonnes pour les articles sur la page d’accueil.', 'lejournaldesactus' ),
] );
