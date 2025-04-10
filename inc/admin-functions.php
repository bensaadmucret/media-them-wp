<?php
/**
 * Fonctions d'administration pour Le Journal des Actus
 *
 * @package LeJournalDesActus
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Enregistrer les styles d'administration
 */
function lejournaldesactus_enqueue_admin_styles() {
    wp_enqueue_style(
        'lejournaldesactus-admin-styles',
        get_template_directory_uri() . '/assets/css/admin-styles.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('admin_enqueue_scripts', 'lejournaldesactus_enqueue_admin_styles');

/**
 * Ajouter une classe dynamique aux éléments en fonction de leur état
 */
function lejournaldesactus_admin_scripts() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Gestion des affichages conditionnels pour les widgets et métaboxes
        function toggleVisibility(triggerSelector, targetSelector, value) {
            $(triggerSelector).on('change', function() {
                if ($(this).val() === value) {
                    $(targetSelector).addClass('active');
                } else {
                    $(targetSelector).removeClass('active');
                }
            });
            // Initialisation
            if ($(triggerSelector).val() === value) {
                $(targetSelector).addClass('active');
            }
        }

        // Widget publicitaire
        toggleVisibility('select[name*="[ad_type]"]', '.image-fields', 'image');
        toggleVisibility('select[name*="[ad_type]"]', '.code-fields', 'code');

        // Widget newsletter
        toggleVisibility('select[name*="[service]"]', '.mailchimp-fields', 'mailchimp');
        toggleVisibility('select[name*="[service]"]', '.custom-fields', 'custom');

        // Métabox articles associés
        toggleVisibility('input[name="related_method"]:checked', '#category-selector', 'category');
        toggleVisibility('input[name="related_method"]:checked', '#tag-selector', 'tag');
        
        $('input[name="related_method"]').on('change', function() {
            var method = $('input[name="related_method"]:checked').val();
            if (method === 'category') {
                $('#category-selector').addClass('active');
                $('#tag-selector').removeClass('active');
            } else if (method === 'tag') {
                $('#tag-selector').addClass('active');
                $('#category-selector').removeClass('active');
            }
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'lejournaldesactus_admin_scripts');

/**
 * Fonction pour générer un badge de temps de lecture avec une couleur personnalisée
 *
 * @param string $reading_time Texte du temps de lecture
 * @param string $color Couleur du badge ou de l'icône
 * @param string $style Style d'affichage ('icon' ou 'badge')
 * @return string HTML du badge de temps de lecture
 */
function lejournaldesactus_reading_time_badge($reading_time, $color = '#f75815', $style = 'icon') {
    $output = '';
    
    if ($style === 'icon') {
        $output = '<span class="reading-time reading-time-icon">';
        $output .= '<i class="bi bi-clock"></i> ' . esc_html($reading_time);
        $output .= '</span>';
    } else {
        $output = '<span class="reading-time reading-time-badge">';
        $output .= esc_html($reading_time);
        $output .= '</span>';
    }
    
    return $output;
}
