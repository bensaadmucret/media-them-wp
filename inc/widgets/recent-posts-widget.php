<?php
/**
 * Widget des articles récents avec image
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe du widget des articles récents avec image
 */
class Lejournaldesactus_Recent_Posts_Widget extends WP_Widget {

    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_recent_posts',
            __('LJDA - Articles Récents', 'lejournaldesactus'),
            array(
                'description' => __('Affiche les articles récents avec image miniature.', 'lejournaldesactus'),
                'classname'   => 'widget-advanced widget-recent-posts',
            )
        );
    }

    /**
     * Affichage du widget dans le frontend
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Articles Récents', 'lejournaldesactus');
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        $show_thumbnail = isset($instance['show_thumbnail']) ? (bool) $instance['show_thumbnail'] : true;
        $category = !empty($instance['category']) ? absint($instance['category']) : 0;

        // Arguments de la requête
        $query_args = array(
            'posts_per_page'      => $number,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
        );

        // Filtrer par catégorie si spécifié
        if ($category > 0) {
            $query_args['cat'] = $category;
        }

        // Exécuter la requête
        $recent_posts = new WP_Query($query_args);

        // Afficher le widget
        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if ($recent_posts->have_posts()) {
            echo '<div class="recent-posts-list">';

            while ($recent_posts->have_posts()) {
                $recent_posts->the_post();
                ?>
                <div class="recent-post">
                    <?php if ($show_thumbnail && has_post_thumbnail()) : ?>
                        <div class="recent-post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('thumbnail'); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="recent-post-content">
                        <h4 class="recent-post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>

                        <?php if ($show_date) : ?>
                            <div class="recent-post-meta">
                                <span class="recent-post-date">
                                    <i class="bi bi-calendar"></i>
                                    <?php echo get_the_date(); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }

            echo '</div>';
        } else {
            echo '<p>' . __('Aucun article récent trouvé.', 'lejournaldesactus') . '</p>';
        }

        // Réinitialiser les données de post
        wp_reset_postdata();

        echo $args['after_widget'];
    }

    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Articles Récents', 'lejournaldesactus');
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        $show_thumbnail = isset($instance['show_thumbnail']) ? (bool) $instance['show_thumbnail'] : true;
        $category = isset($instance['category']) ? absint($instance['category']) : 0;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titre:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Nombre d\'articles à afficher:', 'lejournaldesactus'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Catégorie:', 'lejournaldesactus'); ?></label>
            <?php
            $categories = get_categories(array(
                'orderby'    => 'name',
                'order'      => 'ASC',
                'hide_empty' => false,
            ));
            ?>
            <select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
                <option value="0"><?php _e('Toutes les catégories', 'lejournaldesactus'); ?></option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo $cat->term_id; ?>" <?php selected($category, $cat->term_id); ?>>
                        <?php echo $cat->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Afficher la date', 'lejournaldesactus'); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_thumbnail); ?> id="<?php echo $this->get_field_id('show_thumbnail'); ?>" name="<?php echo $this->get_field_name('show_thumbnail'); ?>" />
            <label for="<?php echo $this->get_field_id('show_thumbnail'); ?>"><?php _e('Afficher l\'image miniature', 'lejournaldesactus'); ?></label>
        </p>
        <?php
    }

    /**
     * Sauvegarde des options du widget
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        $instance['show_date'] = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        $instance['show_thumbnail'] = isset($new_instance['show_thumbnail']) ? (bool) $new_instance['show_thumbnail'] : false;
        $instance['category'] = absint($new_instance['category']);

        return $instance;
    }
}
