<?php
/**
 * Widget des articles populaires
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe du widget des articles populaires
 */
class Lejournaldesactus_Popular_Posts_Widget extends WP_Widget {

    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_popular_posts',
            __('LJDA - Articles Populaires', 'lejournaldesactus'),
            array(
                'description' => __('Affiche les articles les plus populaires basés sur le nombre de vues.', 'lejournaldesactus'),
                'classname'   => 'widget-advanced widget-popular-posts',
            )
        );
    }

    /**
     * Affichage du widget dans le frontend
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Articles Populaires', 'lejournaldesactus');
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        $period = !empty($instance['period']) ? $instance['period'] : 'all';
        $category = !empty($instance['category']) ? absint($instance['category']) : 0;

        // Arguments de la requête
        $query_args = array(
            'posts_per_page'      => $number,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby'             => 'comment_count', // Par défaut, trier par nombre de commentaires
            'order'               => 'DESC',
        );

        // Filtrer par catégorie si spécifié
        if ($category > 0) {
            $query_args['cat'] = $category;
        }

        // Filtrer par période si spécifié
        if ($period !== 'all') {
            $query_args['date_query'] = array(
                'after' => $this->get_date_from_period($period),
            );
        }

        // Si le plugin de compteur de vues est installé, utiliser les vues au lieu des commentaires
        if (function_exists('pvc_get_post_views')) {
            // Récupérer les IDs des articles les plus vus
            $popular_post_ids = $this->get_popular_post_ids($number, $period, $category);
            
            if (!empty($popular_post_ids)) {
                $query_args['post__in'] = $popular_post_ids;
                $query_args['orderby'] = 'post__in'; // Conserver l'ordre des IDs
            }
        }

        // Exécuter la requête
        $popular_posts = new WP_Query($query_args);

        // Afficher le widget
        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if ($popular_posts->have_posts()) {
            echo '<div class="popular-posts-list">';

            $counter = 1;
            while ($popular_posts->have_posts()) {
                $popular_posts->the_post();
                ?>
                <div class="popular-post">
                    <span class="popular-post-number"><?php echo $counter; ?></span>
                    <h4 class="popular-post-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h4>

                    <div class="popular-post-meta">
                        <?php if ($show_date) : ?>
                            <span class="popular-post-date">
                                <i class="bi bi-calendar"></i>
                                <?php echo get_the_date(); ?>
                            </span>
                        <?php endif; ?>

                        <?php if (function_exists('pvc_get_post_views')) : ?>
                            <span class="popular-post-views">
                                <i class="bi bi-eye"></i>
                                <?php echo pvc_get_post_views(get_the_ID()); ?>
                            </span>
                        <?php else : ?>
                            <span class="popular-post-comments">
                                <i class="bi bi-chat"></i>
                                <?php echo get_comments_number(); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                $counter++;
            }

            echo '</div>';
        } else {
            echo '<p>' . __('Aucun article populaire trouvé.', 'lejournaldesactus') . '</p>';
        }

        // Réinitialiser les données de post
        wp_reset_postdata();

        echo $args['after_widget'];
    }

    /**
     * Récupérer les IDs des articles les plus vus
     */
    private function get_popular_post_ids($number, $period, $category) {
        global $wpdb;

        // Vérifier si la fonction de compteur de vues existe
        if (!function_exists('pvc_get_post_views')) {
            return array();
        }

        // Récupérer le nom de la table des vues
        $table_name = $wpdb->prefix . 'post_views';

        // Vérifier si la table existe
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return array();
        }

        // Construire la requête SQL
        $sql = "SELECT post_id FROM $table_name";
        $where_clauses = array();
        $where_clauses[] = "post_id IN (SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post')";

        // Filtrer par période
        if ($period !== 'all') {
            $date = $this->get_date_from_period($period);
            $where_clauses[] = $wpdb->prepare("day >= %s", $date);
        }

        // Ajouter les clauses WHERE
        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }

        // Grouper par post_id et trier par nombre de vues
        $sql .= " GROUP BY post_id ORDER BY SUM(count) DESC LIMIT %d";
        
        // Préparer la requête
        $sql = $wpdb->prepare($sql, $number);
        
        // Exécuter la requête
        $results = $wpdb->get_results($sql);
        
        // Extraire les IDs
        $post_ids = array();
        if (!empty($results)) {
            foreach ($results as $result) {
                // Vérifier la catégorie si nécessaire
                if ($category > 0) {
                    if (has_category($category, $result->post_id)) {
                        $post_ids[] = $result->post_id;
                    }
                } else {
                    $post_ids[] = $result->post_id;
                }
            }
        }
        
        return $post_ids;
    }

    /**
     * Récupérer la date à partir de la période
     */
    private function get_date_from_period($period) {
        $date = '';
        
        switch ($period) {
            case 'day':
                $date = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'week':
                $date = date('Y-m-d', strtotime('-1 week'));
                break;
            case 'month':
                $date = date('Y-m-d', strtotime('-1 month'));
                break;
            case 'year':
                $date = date('Y-m-d', strtotime('-1 year'));
                break;
        }
        
        return $date;
    }

    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Articles Populaires', 'lejournaldesactus');
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        $period = isset($instance['period']) ? $instance['period'] : 'all';
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
            <label for="<?php echo $this->get_field_id('period'); ?>"><?php _e('Période:', 'lejournaldesactus'); ?></label>
            <select id="<?php echo $this->get_field_id('period'); ?>" name="<?php echo $this->get_field_name('period'); ?>">
                <option value="all" <?php selected($period, 'all'); ?>><?php _e('Tout le temps', 'lejournaldesactus'); ?></option>
                <option value="day" <?php selected($period, 'day'); ?>><?php _e('Aujourd\'hui', 'lejournaldesactus'); ?></option>
                <option value="week" <?php selected($period, 'week'); ?>><?php _e('Cette semaine', 'lejournaldesactus'); ?></option>
                <option value="month" <?php selected($period, 'month'); ?>><?php _e('Ce mois-ci', 'lejournaldesactus'); ?></option>
                <option value="year" <?php selected($period, 'year'); ?>><?php _e('Cette année', 'lejournaldesactus'); ?></option>
            </select>
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

        <?php if (!function_exists('pvc_get_post_views')) : ?>
            <p class="description">
                <?php _e('Note: Pour un meilleur comptage des articles populaires, installez le plugin "Post Views Counter".', 'lejournaldesactus'); ?>
            </p>
        <?php endif; ?>
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
        $instance['period'] = sanitize_text_field($new_instance['period']);
        $instance['category'] = absint($new_instance['category']);

        return $instance;
    }
}
