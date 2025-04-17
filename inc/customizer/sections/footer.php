<?php
// Section Footer
Kirki::add_section( 'lejournaldesactus_footer', [
    'title'    => esc_html__( 'Pied de page', 'lejournaldesactus' ),
    'panel'    => 'lejournaldesactus_appearance',
    'priority' => 120,
] );
// Couleur de fond du footer
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'color',
    'settings' => 'lejournaldesactus_footer_bg',
    'label'    => esc_html__( 'Fond du footer', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'default'  => '#222',
    'priority' => 10,
    'output'   => [
        [ 'element' => '#footer', 'property' => 'background-color', 'suffix' => ' !important' ],
    ],
] );
// Couleur du texte du footer
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'color',
    'settings' => 'lejournaldesactus_footer_color',
    'label'    => esc_html__( 'Texte du footer', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'default'  => '#fff',
    'priority' => 20,
    'output'   => [
        [ 'element' => '#footer', 'property' => 'color', 'suffix' => ' !important' ],
    ],
] );
// Copyright personnalisé
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_footer_copyright',
    'label'    => esc_html__( 'Texte du copyright', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'priority' => 30,
    'default'  => ' ' . date('Y') . ' Le Journal des Actus. Tous droits réservés.',
] );
// Icônes sociales à afficher
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'multicheck',
    'settings' => 'lejournaldesactus_footer_socials',
    'label'    => esc_html__( 'Icônes sociales à afficher', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'priority' => 40,
    'default'  => [ 'facebook', 'twitter', 'instagram' ],
    'choices'  => [
        'facebook'  => 'Facebook',
        'twitter'   => 'Twitter',
        'instagram' => 'Instagram',
        'linkedin'  => 'LinkedIn',
        'youtube'   => 'YouTube',
    ],
] );
// Champs URL pour chaque réseau social activé
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_footer_facebook_url',
    'label'    => esc_html__( 'Lien Facebook', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'priority' => 41,
    'default'  => '',
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_footer_twitter_url',
    'label'    => esc_html__( 'Lien Twitter', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'priority' => 42,
    'default'  => '',
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_footer_instagram_url',
    'label'    => esc_html__( 'Lien Instagram', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'priority' => 43,
    'default'  => '',
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_footer_linkedin_url',
    'label'    => esc_html__( 'Lien LinkedIn', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'priority' => 44,
    'default'  => '',
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_footer_youtube_url',
    'label'    => esc_html__( 'Lien YouTube', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'priority' => 45,
    'default'  => '',
] );
// Image d’arrière-plan du footer
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'image',
    'settings' => 'lejournaldesactus_footer_bg_image',
    'label'    => esc_html__( 'Image d’arrière-plan du footer', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_footer',
    'priority' => 50,
    'output'   => [
        [ 'element' => '#footer', 'property' => 'background-image' ],
    ],
] );
