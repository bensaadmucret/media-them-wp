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
                'default-text-color' => '000000',
                'width'              => 1920,
                'height'             => 250,
                'flex-height'        => true,
                'wp-head-callback'   => 'lejournaldesactus_header_style',
            )
        )
    );
}
add_action('after_setup_theme', 'lejournaldesactus_custom_header_setup');

/**
 * Styles pour l'en-tête personnalisé
 */
if (!function_exists('lejournaldesactus_header_style')) :
    function lejournaldesactus_header_style() {
        $header_text_color = get_header_textcolor();

        // Si aucune couleur personnalisée n'est définie, on sort
        if (get_theme_support('custom-header', 'default-text-color') === $header_text_color) {
            return;
        }

        // Si nous arrivons ici, nous avons une couleur personnalisée à appliquer
        ?>
        <style type="text/css">
        <?php
        // Le texte de l'en-tête doit-il être affiché ?
        if (!display_header_text()) :
            ?>
            .site-title,
            .site-description {
                position: absolute;
                clip: rect(1px, 1px, 1px, 1px);
            }
        <?php
        // Si l'utilisateur a défini une couleur de texte personnalisée, on l'utilise
        else :
            ?>
            .site-title a,
            .site-description {
                color: #<?php echo esc_attr($header_text_color); ?>;
            }
        <?php endif; ?>
        </style>
        <?php
    }
endif;
