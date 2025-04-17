<?php
// Section RGPD & Confidentialité
Kirki::add_section( 'lejournaldesactus_rgpd', [
    'title'    => esc_html__( 'RGPD & Confidentialité', 'lejournaldesactus' ),
    'priority' => 120,
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'textarea',
    'settings' => 'lejournaldesactus_cookie_text',
    'label'    => esc_html__( 'Texte de la bannière de cookies', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_rgpd',
    'default'  => esc_html__( 'Nous utilisons des cookies pour améliorer votre expérience sur notre site. En continuant à naviguer, vous acceptez notre utilisation des cookies.', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_cookie_accept_text',
    'label'    => esc_html__( 'Texte du bouton d\'acceptation', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_rgpd',
    'default'  => esc_html__( 'J\'accepte', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'text',
    'settings' => 'lejournaldesactus_privacy_link',
    'label'    => esc_html__( 'Texte du lien vers la politique de confidentialité', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_rgpd',
    'default'  => esc_html__( 'Politique de confidentialité', 'lejournaldesactus' ),
] );
Kirki::add_field( LEJOURNALDESACTUS_KIRKI_CONFIG, [
    'type'     => 'dropdown-pages',
    'settings' => 'lejournaldesactus_privacy_page',
    'label'    => esc_html__( 'Page de politique de confidentialité', 'lejournaldesactus' ),
    'section'  => 'lejournaldesactus_rgpd',
    'default'  => 0,
] );
