<?php
// Section Articles liés
Kirki::add_section( 'lejournaldesactus_related_posts_section', [
    'title'    => esc_html__( '🔗 Articles Liés', 'lejournaldesactus' ),
    'priority' => 160,
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_show_related_posts',
    'label'    => esc_html__( 'Afficher les articles liés', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_related_posts_section',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
    'description' => esc_html__( 'Afficher des suggestions d\'articles similaires à la fin de chaque article.', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_related_posts_title',
    'label'    => esc_html__( 'Titre de la section', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_related_posts_section',
    'default'  => esc_html__( 'Articles similaires', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'number',
    'settings' => 'lejournaldesactus_related_posts_count',
    'label'    => esc_html__( 'Nombre d\'articles à afficher', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_related_posts_section',
    'default'  => 3,
    'choices'  => [
        'min'  => 1,
        'max'  => 9,
        'step' => 1,
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_related_posts_show_thumbnail',
    'label'    => esc_html__( 'Afficher les images miniatures', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_related_posts_section',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_related_posts_show_date',
    'label'    => esc_html__( 'Afficher la date', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_related_posts_section',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'switch',
    'settings' => 'lejournaldesactus_related_posts_show_excerpt',
    'label'    => esc_html__( 'Afficher l\'extrait', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_related_posts_section',
    'default'  => true,
    'choices'  => [
        'on'  => esc_html__( 'Oui', 'lejournaldesactus' ),
        'off' => esc_html__( 'Non', 'lejournaldesactus' ),
    ],
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'select',
    'settings' => 'lejournaldesactus_related_posts_method',
    'label'    => esc_html__( 'Méthode de sélection', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_related_posts_section',
    'default'  => 'combined',
    'choices'  => [
        'categories' => esc_html__( 'Par catégories', 'lejournaldesactus' ),
        'tags'       => esc_html__( 'Par tags', 'lejournaldesactus' ),
        'combined'   => esc_html__( 'Catégories et tags', 'lejournaldesactus' ),
        'content'    => esc_html__( 'Analyse du contenu', 'lejournaldesactus' ),
    ],
] );
