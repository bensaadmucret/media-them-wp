<?php
/**
 * Fonctions pour l'en-tête personnalisé
 *
 * @package LeJournalDesActus
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Configuration de l'en-tête personnalisé
 */
function lejournaldesactus_custom_header_setup() {
    add_theme_support(
        'custom-header',
        apply_filters(
            'lejournaldesactus_custom_header_args',
            array(
                'default-image'      => '',
                'width'              => 1920,
                'height'             => 250,
                'flex-height'        => true,
            )
        )
    );
}
add_action('after_setup_theme', 'lejournaldesactus_custom_header_setup');

/**
 * Styles pour l'en-tête personnalisé
 */
if (function_exists('lejournaldesactus_header_style')) {
    // On ne hook plus cette fonction
}
