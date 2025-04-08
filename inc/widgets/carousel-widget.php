<?php
/**
 * Widget pour afficher un carrousel
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe du widget Carrousel
 */
class Lejournaldesactus_Carousel_Widget extends WP_Widget {
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_carousel_widget',
            __('LJA - Carrousel', 'lejournaldesactus'),
            array(
                'description' => __('Affiche un carrousel dans une zone de widgets.', 'lejournaldesactus'),
                'classname' => 'widget_carousel',
            )
        );
    }

    /**
     * Affichage du widget
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $carousel_id = !empty($instance['carousel_id']) ? absint($instance['carousel_id']) : 0;
        
        // Si aucun carrousel n'est sélectionné, ne rien afficher
        if (!$carousel_id) {
            return;
        }
        
        // Vérifier si le carrousel existe
        $carousel = get_post($carousel_id);
        if (!$carousel || $carousel->post_type !== 'lejournaldesactus_carousel') {
            return;
        }
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        // Afficher le carrousel
        echo do_shortcode('[lejournaldesactus_carousel id="' . esc_attr($carousel_id) . '"]');
        
        echo $args['after_widget'];
    }

    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $carousel_id = !empty($instance['carousel_id']) ? absint($instance['carousel_id']) : 0;
        
        // Récupérer tous les carrousels
        $carousels = get_posts(array(
            'post_type' => 'lejournaldesactus_carousel',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Titre:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('carousel_id')); ?>"><?php esc_html_e('Carrousel:', 'lejournaldesactus'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('carousel_id')); ?>" name="<?php echo esc_attr($this->get_field_name('carousel_id')); ?>">
                <option value="0"><?php esc_html_e('-- Sélectionner un carrousel --', 'lejournaldesactus'); ?></option>
                <?php foreach ($carousels as $carousel) : ?>
                    <option value="<?php echo esc_attr($carousel->ID); ?>" <?php selected($carousel_id, $carousel->ID); ?>><?php echo esc_html($carousel->post_title); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        
        <?php if (empty($carousels)) : ?>
            <p>
                <?php esc_html_e('Aucun carrousel n\'a été créé.', 'lejournaldesactus'); ?>
                <a href="<?php echo esc_url(admin_url('post-new.php?post_type=lejournaldesactus_carousel')); ?>"><?php esc_html_e('Créer un carrousel', 'lejournaldesactus'); ?></a>
            </p>
        <?php endif; ?>
        <?php
    }

    /**
     * Sauvegarde des options du widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['carousel_id'] = !empty($new_instance['carousel_id']) ? absint($new_instance['carousel_id']) : 0;
        
        return $instance;
    }
}
