<?php
/**
 * Metabox pour sélectionner un carrousel dans les pages
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Ajouter une metabox pour sélectionner un carrousel
 */
function lejournaldesactus_add_carousel_metabox() {
    // Ajouter uniquement aux pages
    add_meta_box(
        'lejournaldesactus_page_carousel',
        __('Sélectionner un carrousel', 'lejournaldesactus'),
        'lejournaldesactus_carousel_metabox_callback',
        'page',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'lejournaldesactus_add_carousel_metabox');

/**
 * Callback pour la metabox de sélection de carrousel
 */
function lejournaldesactus_carousel_metabox_callback($post) {
    wp_nonce_field('lejournaldesactus_save_carousel_meta', 'lejournaldesactus_carousel_meta_nonce');
    
    // Récupérer l'ID du carrousel sélectionné
    $carousel_id = get_post_meta($post->ID, '_page_carousel_id', true);
    
    // Récupérer tous les carrousels
    $carousels = get_posts(array(
        'post_type' => 'lejournaldesactus_carousel',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ));
    
    // Vérifier si le template actuel est le template avec carrousel
    $current_template = get_post_meta($post->ID, '_wp_page_template', true);
    $is_carousel_template = ($current_template === 'template-carousel.php');
    
    if ($is_carousel_template) {
        echo '<p class="howto">' . __('Sélectionnez un carrousel à afficher en haut de cette page.', 'lejournaldesactus') . '</p>';
        
        if (empty($carousels)) {
            echo '<p>' . __('Aucun carrousel n\'a été créé. Veuillez d\'abord créer un carrousel.', 'lejournaldesactus') . '</p>';
            echo '<a href="' . admin_url('post-new.php?post_type=lejournaldesactus_carousel') . '" class="button">' . __('Créer un carrousel', 'lejournaldesactus') . '</a>';
        } else {
            echo '<select name="page_carousel_id" class="widefat">';
            echo '<option value="">' . __('-- Sélectionner un carrousel --', 'lejournaldesactus') . '</option>';
            
            foreach ($carousels as $carousel) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($carousel->ID),
                    selected($carousel_id, $carousel->ID, false),
                    esc_html($carousel->post_title)
                );
            }
            
            echo '</select>';
        }
    } else {
        echo '<p>' . __('Pour utiliser un carrousel, veuillez d\'abord sélectionner le modèle "Page avec Carrousel" dans les attributs de la page.', 'lejournaldesactus') . '</p>';
    }
}

/**
 * Sauvegarder les données de la metabox
 */
function lejournaldesactus_save_carousel_meta($post_id) {
    // Vérifier si c'est une sauvegarde automatique
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Vérifier le nonce
    if (!isset($_POST['lejournaldesactus_carousel_meta_nonce']) || !wp_verify_nonce($_POST['lejournaldesactus_carousel_meta_nonce'], 'lejournaldesactus_save_carousel_meta')) {
        return;
    }
    
    // Vérifier les permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Vérifier si le template est celui avec carrousel
    $current_template = get_post_meta($post_id, '_wp_page_template', true);
    if ($current_template !== 'template-carousel.php') {
        return;
    }
    
    // Sauvegarder l'ID du carrousel
    if (isset($_POST['page_carousel_id'])) {
        $carousel_id = sanitize_text_field($_POST['page_carousel_id']);
        update_post_meta($post_id, '_page_carousel_id', $carousel_id);
    }
}
add_action('save_post', 'lejournaldesactus_save_carousel_meta');
