<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Section Blog
Kirki::add_section( 'lejournaldesactus_blog', [
    'title'    => esc_html__( 'Options du Blog', 'lejournaldesactus' ),
    'panel'    => 'lejournaldesactus_appearance',
    'priority' => 30,
] );

// Mode sombre automatique selon l'heure
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_blog_darkmode_auto',
    'label'    => esc_html__( 'Activer le mode sombre automatique (selon l\'heure)', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_blog',
    'default'  => false,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
    'description' => esc_html__( 'Le mode sombre sera automatiquement activé la nuit (20h-7h).', 'lejournaldesactus' ),
] );

// Afficher le temps de lecture
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_blog_reading_time',
    'label'    => esc_html__( 'Afficher le temps de lecture', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_blog',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
    'description' => esc_html__( 'Affiche une estimation du temps de lecture pour chaque article.', 'lejournaldesactus' ),
] );

// Badge "Nouveau"
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_blog_badge_new',
    'label'    => esc_html__( 'Afficher le badge “Nouveau”', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_blog',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
    'description' => esc_html__( 'Affiche un badge “Nouveau” sur les articles récents.', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'slider',
    'settings' => 'lejournaldesactus_blog_badge_new_days',
    'label'    => esc_html__( 'Nombre de jours pour “Nouveau”', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_blog',
    'default'  => 3,
    'choices'  => [ 'min' => 1, 'max' => 14, 'step' => 1 ],
    'description' => esc_html__( 'Un article publié depuis moins de X jours aura le badge “Nouveau”.', 'lejournaldesactus' ),
] );

// Badge "Populaire"
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_blog_badge_popular',
    'label'    => esc_html__( 'Afficher le badge “Populaire”', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_blog',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
    'description' => esc_html__( 'Affiche un badge “Populaire” sur les articles ayant beaucoup de vues.', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'number',
    'settings' => 'lejournaldesactus_blog_badge_popular_threshold',
    'label'    => esc_html__( 'Seuil de vues pour “Populaire”', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_blog',
    'default'  => 500,
    'choices'  => [ 'min' => 50, 'max' => 10000, 'step' => 10 ],
    'description' => esc_html__( 'Un article avec plus de X vues aura le badge “Populaire”.', 'lejournaldesactus' ),
] );
