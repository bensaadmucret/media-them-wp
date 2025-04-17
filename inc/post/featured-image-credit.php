<?php
/**
 * Ajouter le champ de crédit photo pour les images
 */

// Ajouter le champ de crédit dans le formulaire d'édition des médias
if (!function_exists('add_credit_field_to_media_upload')) {
    function add_credit_field_to_media_upload($form_fields, $post) {
        $field_value = get_post_meta($post->ID, '_image_credit', true);

        $form_fields['image_credit'] = array(
            'value' => $field_value ? $field_value : '',
            'label' => __('Crédit photo', 'lejournaldesactus'),
            'helps' => __('Ajoutez le crédit pour cette image', 'lejournaldesactus'),
            'input' => 'text'
        );

        return $form_fields;
    }
}
add_filter('attachment_fields_to_edit', 'add_credit_field_to_media_upload', 10, 2);

// Sauvegarder le crédit photo
if (!function_exists('save_image_credit')) {
    function save_image_credit($post, $attachment) {
        if (isset($attachment['image_credit'])) {
            update_post_meta($post['ID'], '_image_credit', sanitize_text_field($attachment['image_credit']));
        }
        return $post;
    }
}
add_filter('attachment_fields_to_save', 'save_image_credit', 10, 2);

// Fonction pour récupérer le crédit photo
if (!function_exists('get_image_credit')) {
    function get_image_credit($image_id = null) {
        if (!$image_id) {
            // Si aucun ID n'est fourni, essayer de récupérer l'ID de l'image mise en avant
            $image_id = get_post_thumbnail_id();
        }
        if ($image_id) {
            return get_post_meta($image_id, '_image_credit', true);
        }
        return '';
    }
}

// Ajouter le crédit à l'affichage de l'image uniquement sur les pages single
if (!function_exists('add_credit_to_image_display')) {
    function add_credit_to_image_display($html, $post_id, $post_thumbnail_id) {
        // Ne rien faire si on n'est pas sur une page single
        if (!is_single()) {
            return $html;
        }

        if (empty($html)) {
            return $html;
        }

        $credit = get_image_credit($post_thumbnail_id);
        if ($credit) {
            // Créer un DOM pour manipuler le HTML de manière sûre
            $dom = new DOMDocument();
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            
            // Créer le wrapper
            $wrapper = $dom->createElement('div');
            $wrapper->setAttribute('class', 'featured-image-wrapper');
            
            // Déplacer l'image dans le wrapper
            $image = $dom->getElementsByTagName('img')->item(0);
            if ($image) {
                $image_parent = $image->parentNode;
                $wrapper->appendChild($image->cloneNode(true));
                
                // Ajouter le crédit
                $credit_div = $dom->createElement('div');
                $credit_div->setAttribute('class', 'featured-image-credit');
                $credit_div->textContent = $credit;
                $wrapper->appendChild($credit_div);
                
                // Remplacer l'image originale par le wrapper
                if ($image_parent) {
                    $image_parent->replaceChild($wrapper, $image);
                } else {
                    // Si pas de parent, remplacer tout le HTML
                    $dom->appendChild($wrapper);
                }
                
                // Récupérer le HTML modifié
                $html = $dom->saveHTML();
            }
        }
        
        return $html;
    }
}
add_filter('post_thumbnail_html', 'add_credit_to_image_display', 10, 3);
