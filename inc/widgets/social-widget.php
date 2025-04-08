<?php
/**
 * Widget des réseaux sociaux
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe du widget des réseaux sociaux
 */
class Lejournaldesactus_Social_Widget extends WP_Widget {

    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_social',
            __('LJDA - Réseaux Sociaux', 'lejournaldesactus'),
            array(
                'description' => __('Affiche les liens vers vos réseaux sociaux.', 'lejournaldesactus'),
                'classname'   => 'widget-advanced widget-social',
            )
        );
    }

    /**
     * Affichage du widget dans le frontend
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Suivez-nous', 'lejournaldesactus');
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);
        $facebook = !empty($instance['facebook']) ? $instance['facebook'] : '';
        $twitter = !empty($instance['twitter']) ? $instance['twitter'] : '';
        $instagram = !empty($instance['instagram']) ? $instance['instagram'] : '';
        $linkedin = !empty($instance['linkedin']) ? $instance['linkedin'] : '';
        $youtube = !empty($instance['youtube']) ? $instance['youtube'] : '';
        $pinterest = !empty($instance['pinterest']) ? $instance['pinterest'] : '';
        $tiktok = !empty($instance['tiktok']) ? $instance['tiktok'] : '';
        $show_labels = isset($instance['show_labels']) ? (bool) $instance['show_labels'] : false;
        $style = !empty($instance['style']) ? $instance['style'] : 'circle';

        // Vérifier s'il y a au moins un réseau social
        if (empty($facebook) && empty($twitter) && empty($instagram) && empty($linkedin) && empty($youtube) && empty($pinterest) && empty($tiktok)) {
            return;
        }

        // Afficher le widget
        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo '<div class="social-links social-style-' . esc_attr($style) . '">';

        // Facebook
        if (!empty($facebook)) {
            echo '<a href="' . esc_url($facebook) . '" class="social-link facebook" target="_blank" rel="noopener noreferrer">';
            echo '<i class="bi bi-facebook"></i>';
            if ($show_labels) {
                echo '<span class="social-label">' . __('Facebook', 'lejournaldesactus') . '</span>';
            }
            echo '</a>';
        }

        // Twitter
        if (!empty($twitter)) {
            echo '<a href="' . esc_url($twitter) . '" class="social-link twitter" target="_blank" rel="noopener noreferrer">';
            echo '<i class="bi bi-twitter-x"></i>';
            if ($show_labels) {
                echo '<span class="social-label">' . __('Twitter', 'lejournaldesactus') . '</span>';
            }
            echo '</a>';
        }

        // Instagram
        if (!empty($instagram)) {
            echo '<a href="' . esc_url($instagram) . '" class="social-link instagram" target="_blank" rel="noopener noreferrer">';
            echo '<i class="bi bi-instagram"></i>';
            if ($show_labels) {
                echo '<span class="social-label">' . __('Instagram', 'lejournaldesactus') . '</span>';
            }
            echo '</a>';
        }

        // LinkedIn
        if (!empty($linkedin)) {
            echo '<a href="' . esc_url($linkedin) . '" class="social-link linkedin" target="_blank" rel="noopener noreferrer">';
            echo '<i class="bi bi-linkedin"></i>';
            if ($show_labels) {
                echo '<span class="social-label">' . __('LinkedIn', 'lejournaldesactus') . '</span>';
            }
            echo '</a>';
        }

        // YouTube
        if (!empty($youtube)) {
            echo '<a href="' . esc_url($youtube) . '" class="social-link youtube" target="_blank" rel="noopener noreferrer">';
            echo '<i class="bi bi-youtube"></i>';
            if ($show_labels) {
                echo '<span class="social-label">' . __('YouTube', 'lejournaldesactus') . '</span>';
            }
            echo '</a>';
        }

        // Pinterest
        if (!empty($pinterest)) {
            echo '<a href="' . esc_url($pinterest) . '" class="social-link pinterest" target="_blank" rel="noopener noreferrer">';
            echo '<i class="bi bi-pinterest"></i>';
            if ($show_labels) {
                echo '<span class="social-label">' . __('Pinterest', 'lejournaldesactus') . '</span>';
            }
            echo '</a>';
        }

        // TikTok
        if (!empty($tiktok)) {
            echo '<a href="' . esc_url($tiktok) . '" class="social-link tiktok" target="_blank" rel="noopener noreferrer">';
            echo '<i class="bi bi-tiktok"></i>';
            if ($show_labels) {
                echo '<span class="social-label">' . __('TikTok', 'lejournaldesactus') . '</span>';
            }
            echo '</a>';
        }

        echo '</div>';

        echo $args['after_widget'];
    }

    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Suivez-nous', 'lejournaldesactus');
        $facebook = isset($instance['facebook']) ? $instance['facebook'] : '';
        $twitter = isset($instance['twitter']) ? $instance['twitter'] : '';
        $instagram = isset($instance['instagram']) ? $instance['instagram'] : '';
        $linkedin = isset($instance['linkedin']) ? $instance['linkedin'] : '';
        $youtube = isset($instance['youtube']) ? $instance['youtube'] : '';
        $pinterest = isset($instance['pinterest']) ? $instance['pinterest'] : '';
        $tiktok = isset($instance['tiktok']) ? $instance['tiktok'] : '';
        $show_labels = isset($instance['show_labels']) ? (bool) $instance['show_labels'] : false;
        $style = isset($instance['style']) ? $instance['style'] : 'circle';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titre:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('URL Facebook:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="url" value="<?php echo esc_url($facebook); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('URL Twitter:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="url" value="<?php echo esc_url($twitter); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('instagram'); ?>"><?php _e('URL Instagram:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('instagram'); ?>" name="<?php echo $this->get_field_name('instagram'); ?>" type="url" value="<?php echo esc_url($instagram); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e('URL LinkedIn:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" type="url" value="<?php echo esc_url($linkedin); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e('URL YouTube:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" type="url" value="<?php echo esc_url($youtube); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('pinterest'); ?>"><?php _e('URL Pinterest:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('pinterest'); ?>" name="<?php echo $this->get_field_name('pinterest'); ?>" type="url" value="<?php echo esc_url($pinterest); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('tiktok'); ?>"><?php _e('URL TikTok:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('tiktok'); ?>" name="<?php echo $this->get_field_name('tiktok'); ?>" type="url" value="<?php echo esc_url($tiktok); ?>" />
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_labels); ?> id="<?php echo $this->get_field_id('show_labels'); ?>" name="<?php echo $this->get_field_name('show_labels'); ?>" />
            <label for="<?php echo $this->get_field_id('show_labels'); ?>"><?php _e('Afficher les libellés', 'lejournaldesactus'); ?></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Style:', 'lejournaldesactus'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
                <option value="circle" <?php selected($style, 'circle'); ?>><?php _e('Cercle', 'lejournaldesactus'); ?></option>
                <option value="square" <?php selected($style, 'square'); ?>><?php _e('Carré', 'lejournaldesactus'); ?></option>
                <option value="rounded" <?php selected($style, 'rounded'); ?>><?php _e('Arrondi', 'lejournaldesactus'); ?></option>
                <option value="text" <?php selected($style, 'text'); ?>><?php _e('Texte uniquement', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        <?php
    }

    /**
     * Sauvegarde des options du widget
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['facebook'] = esc_url_raw($new_instance['facebook']);
        $instance['twitter'] = esc_url_raw($new_instance['twitter']);
        $instance['instagram'] = esc_url_raw($new_instance['instagram']);
        $instance['linkedin'] = esc_url_raw($new_instance['linkedin']);
        $instance['youtube'] = esc_url_raw($new_instance['youtube']);
        $instance['pinterest'] = esc_url_raw($new_instance['pinterest']);
        $instance['tiktok'] = esc_url_raw($new_instance['tiktok']);
        $instance['show_labels'] = isset($new_instance['show_labels']) ? (bool) $new_instance['show_labels'] : false;
        $instance['style'] = sanitize_text_field($new_instance['style']);

        return $instance;
    }
}
