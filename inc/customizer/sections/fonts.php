<?php
// Section Polices
Kirki::add_section( 'lejournaldesactus_fonts', [
    'title'    => esc_html__( 'Polices', 'lejournaldesactus' ),
    'panel'    => 'lejournaldesactus_appearance',
    'priority' => 20,
] );
// Police principale (texte)
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'typography',
    'settings' => 'lejournaldesactus_font_family',
    'label'    => esc_html__( 'Police principale (texte)', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_fonts',
    'default'  => [
        'font-family'    => 'Inter',
        'variant'        => 'regular',
        'font-size'      => '18px',
        'line-height'    => '1.6',
        'letter-spacing' => '0',
        'color'          => '#212529',
        'text-transform' => 'none',
        'font-style'     => 'normal',
        'text-decoration'=>'none',
        'text-align'     => 'left',
        'word-spacing'   => 'normal',
    ],
    'priority' => 10,
    'choices'  => [
        'fonts' => [
            'google' => [ 'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Nunito', 'Poppins', 'Raleway', 'Merriweather', 'Space Grotesk', 'Bricolage Grotesque' ],
        ],
    ],
    'output'   => [
        [ 'element' => 'body, .site-main', 'property' => 'font-family' ],
        [ 'element' => 'body, .site-main', 'property' => 'font-size' ],
        [ 'element' => 'body, .site-main', 'property' => 'line-height' ],
        [ 'element' => 'body, .site-main', 'property' => 'letter-spacing' ],
        [ 'element' => 'body, .site-main', 'property' => 'color' ],
        [ 'element' => 'body, .site-main', 'property' => 'text-transform' ],
        [ 'element' => 'body, .site-main', 'property' => 'font-style' ],
        [ 'element' => 'body, .site-main', 'property' => 'text-decoration' ],
        [ 'element' => 'body, .site-main', 'property' => 'text-align' ],
        [ 'element' => 'body, .site-main', 'property' => 'word-spacing' ],
    ],
] );
// Titres (H1-H6)
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'typography',
    'settings' => 'lejournaldesactus_heading_typo',
    'label'    => esc_html__( 'Titres (H1-H6)', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_fonts',
    'default'  => [
        'font-family'    => 'Montserrat',
        'variant'        => '700',
        'font-size'      => '2.2rem',
        'line-height'    => '1.2',
        'letter-spacing' => '0',
        'color'          => '#111',
        'text-transform' => 'none',
        'font-style'     => 'normal',
        'text-decoration'=>'none',
        'text-align'     => 'left',
        'word-spacing'   => 'normal',
    ],
    'priority' => 20,
    'choices'  => [
        'fonts' => [
            'google' => [ 'Montserrat', 'Inter', 'Roboto', 'Poppins', 'Lato', 'Open Sans', 'Merriweather', 'Raleway', 'Space Grotesk', 'Bricolage Grotesque' ],
        ],
    ],
    'output'   => [
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'font-family' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'font-size' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'line-height' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'letter-spacing' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'color' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'text-transform' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'font-style' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'text-decoration' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'text-align' ],
        [ 'element' => 'h1, h2, h3, h4, h5, h6', 'property' => 'word-spacing' ],
    ],
] );
// Menu principal
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'typography',
    'settings' => 'lejournaldesactus_menu_typo',
    'label'    => esc_html__( 'Menu principal', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_fonts',
    'default'  => [
        'font-family'    => 'Poppins',
        'variant'        => '500',
        'font-size'      => '1rem',
        'line-height'    => '1.3',
        'letter-spacing' => '0.02em',
        'color'          => '#222',
        'text-transform' => 'uppercase',
        'font-style'     => 'normal',
        'text-decoration'=>'none',
        'text-align'     => 'left',
        'word-spacing'   => 'normal',
    ],
    'priority' => 30,
    'choices'  => [
        'fonts' => [
            'google' => [ 'Poppins', 'Montserrat', 'Inter', 'Roboto', 'Nunito', 'Lato', 'Open Sans' ],
        ],
    ],
    'output'   => [
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'font-family' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'font-size' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'line-height' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'letter-spacing' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'color' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'text-transform' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'font-style' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'text-decoration' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'text-align' ],
        [ 'element' => '.main-navigation, .menu, .site-header nav', 'property' => 'word-spacing' ],
    ],
] );
// Boutons
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'typography',
    'settings' => 'lejournaldesactus_button_typo',
    'label'    => esc_html__( 'Boutons', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_fonts',
    'default'  => [
        'font-family'    => 'Nunito',
        'variant'        => '700',
        'font-size'      => '1rem',
        'line-height'    => '1.2',
        'letter-spacing' => '0.05em',
        'color'          => '#fff',
        'text-transform' => 'uppercase',
        'font-style'     => 'normal',
        'text-decoration'=>'none',
        'text-align'     => 'center',
        'word-spacing'   => 'normal',
    ],
    'priority' => 40,
    'choices'  => [
        'fonts' => [
            'google' => [ 'Nunito', 'Montserrat', 'Inter', 'Roboto', 'Poppins', 'Lato', 'Open Sans' ],
        ],
    ],
    'output'   => [
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'font-family' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'font-size' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'line-height' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'letter-spacing' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'color' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'text-transform' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'font-style' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'text-decoration' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'text-align' ],
        [ 'element' => 'button, .btn, input[type="submit"], .wp-block-button__link', 'property' => 'word-spacing' ],
    ],
] );
