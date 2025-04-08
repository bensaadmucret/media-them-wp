<?php
/**
 * Widget des auteurs
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe du widget des auteurs
 */
class Lejournaldesactus_Authors_Widget extends WP_Widget {

    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_authors',
            __('LJDA - Auteurs', 'lejournaldesactus'),
            array(
                'description' => __('Affiche les auteurs du site avec leur avatar.', 'lejournaldesactus'),
                'classname'   => 'widget-advanced widget-authors',
            )
        );
    }

    /**
     * Affichage du widget dans le frontend
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Nos Auteurs', 'lejournaldesactus');
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_post_count = isset($instance['show_post_count']) ? (bool) $instance['show_post_count'] : true;
        $orderby = !empty($instance['orderby']) ? $instance['orderby'] : 'post_count';
        $order = !empty($instance['order']) ? $instance['order'] : 'DESC';
        $exclude = !empty($instance['exclude']) ? $instance['exclude'] : '';
        $include = !empty($instance['include']) ? $instance['include'] : '';
        $use_custom_authors = isset($instance['use_custom_authors']) ? (bool) $instance['use_custom_authors'] : false;

        // Afficher le widget
        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // Vérifier si nous utilisons les auteurs personnalisés ou les auteurs WordPress
        if ($use_custom_authors && post_type_exists('author')) {
            // Auteurs personnalisés (CPT)
            $this->display_custom_authors($number, $show_post_count, $orderby, $order, $exclude, $include);
        } else {
            // Auteurs WordPress standard
            $this->display_wp_authors($number, $show_post_count, $orderby, $order, $exclude, $include);
        }

        echo $args['after_widget'];
    }

    /**
     * Afficher les auteurs personnalisés (CPT)
     */
    private function display_custom_authors($number, $show_post_count, $orderby, $order, $exclude, $include) {
        // Arguments de la requête
        $args = array(
            'post_type'      => 'author',
            'posts_per_page' => $number,
            'post_status'    => 'publish',
        );

        // Tri
        if ($orderby === 'post_count') {
            $args['meta_key'] = '_author_articles_count';
            $args['orderby'] = 'meta_value_num';
        } else {
            $args['orderby'] = $orderby;
        }
        $args['order'] = $order;

        // Exclusion/Inclusion
        if (!empty($exclude)) {
            $exclude_ids = array_map('trim', explode(',', $exclude));
            $args['post__not_in'] = $exclude_ids;
        }

        if (!empty($include)) {
            $include_ids = array_map('trim', explode(',', $include));
            $args['post__in'] = $include_ids;
        }

        // Exécuter la requête
        $authors_query = new WP_Query($args);

        if ($authors_query->have_posts()) {
            echo '<div class="authors-list">';

            while ($authors_query->have_posts()) {
                $authors_query->the_post();
                $author_id = get_the_ID();
                $author_name = get_the_title();
                $author_url = get_permalink();
                $author_designation = get_post_meta($author_id, '_author_designation', true);
                $post_count = get_post_meta($author_id, '_author_articles_count', true);
                
                ?>
                <div class="author">
                    <div class="author-avatar">
                        <a href="<?php echo esc_url($author_url); ?>">
                            <?php 
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('thumbnail');
                            } else {
                                echo '<img src="' . esc_url(get_avatar_url($author_id, array('size' => 60))) . '" alt="' . esc_attr($author_name) . '">';
                            }
                            ?>
                        </a>
                    </div>
                    <div class="author-info">
                        <h4 class="author-name">
                            <a href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author_name); ?></a>
                        </h4>
                        <?php if ($author_designation) : ?>
                            <div class="author-designation"><?php echo esc_html($author_designation); ?></div>
                        <?php endif; ?>
                        <?php if ($show_post_count && $post_count) : ?>
                            <div class="author-posts-count">
                                <?php 
                                printf(
                                    _n('%s article', '%s articles', $post_count, 'lejournaldesactus'),
                                    number_format_i18n($post_count)
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }

            echo '</div>';
        } else {
            echo '<p>' . __('Aucun auteur trouvé.', 'lejournaldesactus') . '</p>';
        }

        // Réinitialiser les données de post
        wp_reset_postdata();
    }

    /**
     * Afficher les auteurs WordPress standard
     */
    private function display_wp_authors($number, $show_post_count, $orderby, $order, $exclude, $include) {
        // Arguments pour get_users()
        $args = array(
            'number'  => $number,
            'orderby' => $orderby,
            'order'   => $order,
            'who'     => 'authors',
        );

        // Exclusion/Inclusion
        if (!empty($exclude)) {
            $exclude_ids = array_map('trim', explode(',', $exclude));
            $args['exclude'] = $exclude_ids;
        }

        if (!empty($include)) {
            $include_ids = array_map('trim', explode(',', $include));
            $args['include'] = $include_ids;
        }

        // Récupérer les auteurs
        $authors = get_users($args);

        if (!empty($authors)) {
            echo '<div class="authors-list">';

            foreach ($authors as $author) {
                $author_id = $author->ID;
                $author_name = $author->display_name;
                $author_url = get_author_posts_url($author_id);
                $post_count = count_user_posts($author_id, 'post', true);
                
                // Ne pas afficher les auteurs sans articles
                if ($post_count === 0) {
                    continue;
                }
                
                ?>
                <div class="author">
                    <div class="author-avatar">
                        <a href="<?php echo esc_url($author_url); ?>">
                            <?php echo get_avatar($author_id, 60); ?>
                        </a>
                    </div>
                    <div class="author-info">
                        <h4 class="author-name">
                            <a href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author_name); ?></a>
                        </h4>
                        <?php if ($show_post_count) : ?>
                            <div class="author-posts-count">
                                <?php 
                                printf(
                                    _n('%s article', '%s articles', $post_count, 'lejournaldesactus'),
                                    number_format_i18n($post_count)
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }

            echo '</div>';
        } else {
            echo '<p>' . __('Aucun auteur trouvé.', 'lejournaldesactus') . '</p>';
        }
    }

    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Nos Auteurs', 'lejournaldesactus');
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_post_count = isset($instance['show_post_count']) ? (bool) $instance['show_post_count'] : true;
        $orderby = isset($instance['orderby']) ? $instance['orderby'] : 'post_count';
        $order = isset($instance['order']) ? $instance['order'] : 'DESC';
        $exclude = isset($instance['exclude']) ? $instance['exclude'] : '';
        $include = isset($instance['include']) ? $instance['include'] : '';
        $use_custom_authors = isset($instance['use_custom_authors']) ? (bool) $instance['use_custom_authors'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titre:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Nombre d\'auteurs à afficher:', 'lejournaldesactus'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($use_custom_authors); ?> id="<?php echo $this->get_field_id('use_custom_authors'); ?>" name="<?php echo $this->get_field_name('use_custom_authors'); ?>" />
            <label for="<?php echo $this->get_field_id('use_custom_authors'); ?>"><?php _e('Utiliser les auteurs personnalisés', 'lejournaldesactus'); ?></label>
            <br>
            <small><?php _e('Utilise le type de contenu personnalisé "author" au lieu des utilisateurs WordPress.', 'lejournaldesactus'); ?></small>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Trier par:', 'lejournaldesactus'); ?></label>
            <select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
                <option value="post_count" <?php selected($orderby, 'post_count'); ?>><?php _e('Nombre d\'articles', 'lejournaldesactus'); ?></option>
                <option value="name" <?php selected($orderby, 'name'); ?>><?php _e('Nom', 'lejournaldesactus'); ?></option>
                <option value="date" <?php selected($orderby, 'date'); ?>><?php _e('Date', 'lejournaldesactus'); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Ordre:', 'lejournaldesactus'); ?></label>
            <select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
                <option value="DESC" <?php selected($order, 'DESC'); ?>><?php _e('Décroissant', 'lejournaldesactus'); ?></option>
                <option value="ASC" <?php selected($order, 'ASC'); ?>><?php _e('Croissant', 'lejournaldesactus'); ?></option>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_post_count); ?> id="<?php echo $this->get_field_id('show_post_count'); ?>" name="<?php echo $this->get_field_name('show_post_count'); ?>" />
            <label for="<?php echo $this->get_field_id('show_post_count'); ?>"><?php _e('Afficher le nombre d\'articles', 'lejournaldesactus'); ?></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclure (IDs séparés par des virgules):', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo esc_attr($exclude); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('include'); ?>"><?php _e('Inclure uniquement (IDs séparés par des virgules):', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('include'); ?>" name="<?php echo $this->get_field_name('include'); ?>" type="text" value="<?php echo esc_attr($include); ?>" />
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
        $instance['show_post_count'] = isset($new_instance['show_post_count']) ? (bool) $new_instance['show_post_count'] : false;
        $instance['orderby'] = sanitize_text_field($new_instance['orderby']);
        $instance['order'] = sanitize_text_field($new_instance['order']);
        $instance['exclude'] = sanitize_text_field($new_instance['exclude']);
        $instance['include'] = sanitize_text_field($new_instance['include']);
        $instance['use_custom_authors'] = isset($new_instance['use_custom_authors']) ? (bool) $new_instance['use_custom_authors'] : false;

        return $instance;
    }
}
