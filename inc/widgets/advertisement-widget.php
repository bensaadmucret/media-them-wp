<?php
/**
 * Widget de publicité
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe du widget de publicité
 */
class Lejournaldesactus_Advertisement_Widget extends WP_Widget {

    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_advertisement',
            __('LJDA - Publicité', 'lejournaldesactus'),
            array(
                'description' => __('Affiche une bannière publicitaire avec image ou code HTML.', 'lejournaldesactus'),
                'classname'   => 'widget-advanced widget-advertisement',
            )
        );
    }

    /**
     * Affichage du widget dans le frontend
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);
        $ad_type = !empty($instance['ad_type']) ? $instance['ad_type'] : 'image';
        $image_url = !empty($instance['image_url']) ? $instance['image_url'] : '';
        $destination_url = !empty($instance['destination_url']) ? $instance['destination_url'] : '';
        $alt_text = !empty($instance['alt_text']) ? $instance['alt_text'] : '';
        $ad_code = !empty($instance['ad_code']) ? $instance['ad_code'] : '';
        $ad_size = !empty($instance['ad_size']) ? $instance['ad_size'] : 'medium';
        $show_as_sponsored = isset($instance['show_as_sponsored']) ? (bool) $instance['show_as_sponsored'] : true;

        // Afficher le widget
        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo '<div class="advertisement-wrap ad-size-' . esc_attr($ad_size) . '">';

        // Afficher le contenu en fonction du type de publicité
        if ($ad_type === 'image' && $image_url) {
            // Publicité avec image
            if ($destination_url) {
                echo '<a href="' . esc_url($destination_url) . '" target="_blank" rel="noopener noreferrer sponsored">';
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($alt_text) . '" class="advertisement-image">';
                echo '</a>';
            } else {
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($alt_text) . '" class="advertisement-image">';
            }
        } elseif ($ad_type === 'code' && $ad_code) {
            // Publicité avec code HTML/JavaScript
            echo '<div class="advertisement-code">';
            echo do_shortcode($ad_code);
            echo '</div>';
        }

        // Afficher le label "Publicité" si demandé
        if ($show_as_sponsored) {
            echo '<div class="advertisement-label">' . __('Publicité', 'lejournaldesactus') . '</div>';
        }

        echo '</div>';

        echo $args['after_widget'];
    }

    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $ad_type = isset($instance['ad_type']) ? $instance['ad_type'] : 'image';
        $image_url = isset($instance['image_url']) ? $instance['image_url'] : '';
        $destination_url = isset($instance['destination_url']) ? $instance['destination_url'] : '';
        $alt_text = isset($instance['alt_text']) ? $instance['alt_text'] : '';
        $ad_code = isset($instance['ad_code']) ? $instance['ad_code'] : '';
        $ad_size = isset($instance['ad_size']) ? $instance['ad_size'] : 'medium';
        $show_as_sponsored = isset($instance['show_as_sponsored']) ? (bool) $instance['show_as_sponsored'] : true;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titre (optionnel):', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('ad_type'); ?>"><?php _e('Type de publicité:', 'lejournaldesactus'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('ad_type'); ?>" name="<?php echo $this->get_field_name('ad_type'); ?>">
                <option value="image" <?php selected($ad_type, 'image'); ?>><?php _e('Image', 'lejournaldesactus'); ?></option>
                <option value="code" <?php selected($ad_type, 'code'); ?>><?php _e('Code HTML/JavaScript', 'lejournaldesactus'); ?></option>
            </select>
        </p>

        <div class="image-fields">
            <p>
                <label for="<?php echo $this->get_field_id('image_url'); ?>"><?php _e('URL de l\'image:', 'lejournaldesactus'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('image_url'); ?>" name="<?php echo $this->get_field_name('image_url'); ?>" type="url" value="<?php echo esc_url($image_url); ?>" />
                <button class="upload_image_button button button-secondary" data-input="<?php echo $this->get_field_id('image_url'); ?>"><?php _e('Sélectionner une image', 'lejournaldesactus'); ?></button>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('destination_url'); ?>"><?php _e('URL de destination:', 'lejournaldesactus'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('destination_url'); ?>" name="<?php echo $this->get_field_name('destination_url'); ?>" type="url" value="<?php echo esc_url($destination_url); ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('alt_text'); ?>"><?php _e('Texte alternatif:', 'lejournaldesactus'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('alt_text'); ?>" name="<?php echo $this->get_field_name('alt_text'); ?>" type="text" value="<?php echo esc_attr($alt_text); ?>" />
            </p>
        </div>

        <div class="code-fields">
            <p>
                <label for="<?php echo $this->get_field_id('ad_code'); ?>"><?php _e('Code de publicité:', 'lejournaldesactus'); ?></label>
                <textarea class="widefat" id="<?php echo $this->get_field_id('ad_code'); ?>" name="<?php echo $this->get_field_name('ad_code'); ?>" rows="6"><?php echo esc_textarea($ad_code); ?></textarea>
                <small><?php _e('Collez ici le code HTML ou JavaScript fourni par votre réseau publicitaire.', 'lejournaldesactus'); ?></small>
            </p>
        </div>

        <p>
            <label for="<?php echo $this->get_field_id('ad_size'); ?>"><?php _e('Taille:', 'lejournaldesactus'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('ad_size'); ?>" name="<?php echo $this->get_field_name('ad_size'); ?>">
                <option value="small" <?php selected($ad_size, 'small'); ?>><?php _e('Petite', 'lejournaldesactus'); ?></option>
                <option value="medium" <?php selected($ad_size, 'medium'); ?>><?php _e('Moyenne', 'lejournaldesactus'); ?></option>
                <option value="large" <?php selected($ad_size, 'large'); ?>><?php _e('Grande', 'lejournaldesactus'); ?></option>
                <option value="custom" <?php selected($ad_size, 'custom'); ?>><?php _e('Personnalisée', 'lejournaldesactus'); ?></option>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_as_sponsored); ?> id="<?php echo $this->get_field_id('show_as_sponsored'); ?>" name="<?php echo $this->get_field_name('show_as_sponsored'); ?>" />
            <label for="<?php echo $this->get_field_id('show_as_sponsored'); ?>"><?php _e('Afficher le label "Publicité"', 'lejournaldesactus'); ?></label>
        </p>

        <script>
            jQuery(document).ready(function($) {
                // Afficher/masquer les champs en fonction du type de publicité
                $('#<?php echo $this->get_field_id('ad_type'); ?>').on('change', function() {
                    if ($(this).val() === 'image') {
                        $('.image-fields').show();
                        $('.code-fields').hide();
                    } else {
                        $('.image-fields').hide();
                        $('.code-fields').show();
                    }
                });

                // Gestionnaire pour le bouton de sélection d'image
                $('.upload_image_button').on('click', function(e) {
                    e.preventDefault();
                    var inputField = $(this).data('input');
                    
                    var frame = wp.media({
                        title: '<?php _e('Sélectionner une image', 'lejournaldesactus'); ?>',
                        multiple: false
                    });

                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $('#' + inputField).val(attachment.url);
                    });

                    frame.open();
                });
            });
        </script>
        <?php
    }

    /**
     * Sauvegarde des options du widget
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['ad_type'] = sanitize_text_field($new_instance['ad_type']);
        $instance['image_url'] = esc_url_raw($new_instance['image_url']);
        $instance['destination_url'] = esc_url_raw($new_instance['destination_url']);
        $instance['alt_text'] = sanitize_text_field($new_instance['alt_text']);
        $instance['ad_code'] = $new_instance['ad_code']; // Ne pas sanitizer le code HTML/JS
        $instance['ad_size'] = sanitize_text_field($new_instance['ad_size']);
        $instance['show_as_sponsored'] = isset($new_instance['show_as_sponsored']) ? (bool) $new_instance['show_as_sponsored'] : false;

        return $instance;
    }
}
