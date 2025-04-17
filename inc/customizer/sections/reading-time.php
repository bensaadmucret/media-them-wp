<?php
// Section Temps de lecture
Kirki::add_section( 'lejournaldesactus_reading_time_section', [
    'title'    => esc_html__( '⏱️ Temps de Lecture', 'lejournaldesactus' ),
    'priority' => 170,
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_show_reading_time',
    'label'    => esc_html__( 'Afficher le temps de lecture', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_reading_time_section',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
    'description' => esc_html__( 'Afficher une estimation du temps de lecture pour chaque article.', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'number',
    'settings' => 'lejournaldesactus_reading_time_wpm',
    'label'    => esc_html__( 'Vitesse de lecture (mots par minute)', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_reading_time_section',
    'default'  => 200,
    'choices'  => [
        'min'  => 100,
        'max'  => 500,
        'step' => 10,
    ],
    'description' => esc_html__( 'La vitesse moyenne de lecture est de 200-250 mots par minute.', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_reading_time_include_images',
    'label'    => esc_html__( 'Inclure les images dans le calcul', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_reading_time_section',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
    'description' => esc_html__( 'Ajouter du temps supplémentaire pour chaque image dans l\'article.', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'number',
    'settings' => 'lejournaldesactus_reading_time_image_time',
    'label'    => esc_html__( 'Temps par image (en secondes)', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_reading_time_section',
    'default'  => 12,
    'choices'  => [
        'min'  => 1,
        'max'  => 60,
        'step' => 1,
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'select',
    'settings' => 'lejournaldesactus_reading_time_display',
    'label'    => esc_html__( 'Emplacement de l\'affichage', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_reading_time_section',
    'default'  => 'before_content',
    'choices'  => [
        'before_title'   => esc_html__( 'Avant le titre', 'lejournaldesactus' ),
        'after_title'    => esc_html__( 'Après le titre', 'lejournaldesactus' ),
        'before_content' => esc_html__( 'Avant le contenu', 'lejournaldesactus' ),
        'with_meta'      => esc_html__( 'Dans les métadonnées', 'lejournaldesactus' ),
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_reading_time_prefix',
    'label'    => esc_html__( 'Préfixe', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_reading_time_section',
    'default'  => esc_html__( 'Temps de lecture :', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_reading_time_suffix',
    'label'    => esc_html__( 'Suffixe', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_reading_time_section',
    'default'  => esc_html__( 'min de lecture', 'lejournaldesactus' ),
] );
