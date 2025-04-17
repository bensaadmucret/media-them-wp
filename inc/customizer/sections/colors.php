<?php
// Panel principal Apparence générale
Kirki::add_panel( 'lejournaldesactus_appearance', [
    'priority'    => 1,
    'title'       => esc_html__( 'Apparence générale', 'lejournaldesactus' ),
] );

// Section Couleurs
Kirki::add_section( 'lejournaldesactus_colors', [
    'title'    => esc_html__( 'Couleurs', 'lejournaldesactus' ),
    'panel'    => 'lejournaldesactus_appearance',
    'priority' => 10,
] );

Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'color',
    'settings' => 'lejournaldesactus_primary_color',
    'label'    => esc_html__( 'Couleur principale', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_colors',
    'default'  => '#f75815',
    'priority' => 10,
    'output'   => [
        [ 'element' => ':root', 'property' => '--primary-color' ],
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'color',
    'settings' => 'lejournaldesactus_secondary_color',
    'label'    => esc_html__( 'Couleur secondaire', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_colors',
    'default'  => '#A2F8B5',
    'priority' => 20,
    'output'   => [
        [ 'element' => ':root', 'property' => '--secondary-color' ],
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'color',
    'settings' => 'lejournaldesactus_bg_color',
    'label'    => esc_html__( 'Couleur de fond', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_colors',
    'default'  => '#fff',
    'priority' => 30,
    'output'   => [
        [ 'element' => ':root', 'property' => '--body-bg' ],
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'color',
    'settings' => 'lejournaldesactus_text_color',
    'label'    => esc_html__( 'Couleur du texte', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_colors',
    'default'  => '#212529',
    'priority' => 40,
    'output'   => [
        [ 'element' => ':root', 'property' => '--text-color' ],
    ],
] );
// Image d’arrière-plan du site
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'image',
    'settings' => 'lejournaldesactus_site_bg_image',
    'label'    => esc_html__( 'Image d’arrière-plan du site', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_colors',
    'priority' => 80,
    'output'   => [
        [ 'element' => 'body', 'property' => 'background-image' ],
    ],
] );
// Favicon personnalisé
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'image',
    'settings' => 'lejournaldesactus_favicon',
    'label'    => esc_html__( 'Favicon personnalisé', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_colors',
    'priority' => 90,
] );
// Champ Kirki de test
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_test_field',
    'label'    => esc_html__( 'Champ de test Kirki', 'lejournaldesactus' ),
    'section'  => 'title_tagline',
    'default'  => 'Ceci est un test',
    'priority' => 999,
] );
