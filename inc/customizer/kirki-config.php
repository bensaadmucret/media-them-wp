<?php
// Kirki configuration for Le Journal des Actus
if ( ! defined( 'ABSPATH' ) ) exit;

define('LEJOURNALDESACTUS_KIRKI_CONFIG', 'lejournaldesactus');

// DEBUG LOG : Afficher les fichiers chargés et détecter les erreurs dans debug.log
add_action('after_setup_theme', function() {
    if ( defined('WP_DEBUG') && WP_DEBUG ) {
        $section_files = glob(__DIR__ . '/sections/*.php');
        error_log('--- Debug Customizer - Fichiers chargés ---');
        foreach ($section_files as $section_file) {
            try {
                require_once $section_file;
                error_log(basename($section_file) . ' ... OK');
            } catch (Throwable $e) {
                error_log(basename($section_file) . ' ... ERREUR : ' . $e->getMessage());
            }
        }
        error_log('--- Fin Debug Customizer ---');
    }

    // Sécurité : n'exécute Kirki que si la classe est chargée
    if ( class_exists('Kirki') ) {
        Kirki::add_config( LEJOURNALDESACTUS_KIRKI_CONFIG, [
            'capability'    => 'edit_theme_options',
            'option_type'   => 'theme_mod',
        ]);
        // Inclusion automatique de toutes les sections du Customizer
        foreach (glob(__DIR__ . '/sections/*.php') as $section_file) {
            require_once $section_file;
        }
    }
});
