<?php
/**
 * Système de détection et d'affichage des articles tendance
 * 
 * Ce fichier contient les fonctions pour détecter automatiquement
 * les articles les plus populaires et les mettre en avant sur le site.
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour gérer les articles tendance
 */
class Lejournaldesactus_Trending_Posts {
    /**
     * Initialisation de la classe
     */
    public function __construct() {
        // Ajouter un hook pour mettre à jour les compteurs de vues
        add_action('wp_head', array($this, 'track_post_views'));
        
        // Ajouter un hook pour planifier la mise à jour des articles tendance
        add_action('wp', array($this, 'schedule_trending_calculation'));
        
        // Ajouter le hook pour calculer les articles tendance
        add_action('lejournaldesactus_calculate_trending', array($this, 'calculate_trending_posts'));
        
        // Ajouter un shortcode pour afficher les articles tendance
        add_shortcode('lejournaldesactus_trending', array($this, 'trending_shortcode'));
        
        // Ajouter un widget pour les articles tendance
        add_action('widgets_init', array($this, 'register_trending_widget'));
    }
    
    /**
     * Planifier le calcul des articles tendance
     */
    public function schedule_trending_calculation() {
        if (!wp_next_scheduled('lejournaldesactus_calculate_trending')) {
            wp_schedule_event(time(), 'hourly', 'lejournaldesactus_calculate_trending');
        }
    }
    
    /**
     * Suivre les vues des articles
     */
    public function track_post_views() {
        if (is_single() && !current_user_can('edit_posts')) {
            global $post;
            
            // Récupérer le nombre actuel de vues
            $views = get_post_meta($post->ID, 'lejournaldesactus_post_views', true);
            
            if ($views === '') {
                $views = 0;
            }
            
            // Incrémenter et mettre à jour
            $views++;
            update_post_meta($post->ID, 'lejournaldesactus_post_views', $views);
            
            // Mettre à jour les vues récentes (dernières 24h)
            $this->update_recent_views($post->ID);
        }
    }
    
    /**
     * Mettre à jour les vues récentes
     */
    private function update_recent_views($post_id) {
        $recent_views = get_post_meta($post_id, 'lejournaldesactus_recent_views', true);
        
        if (!is_array($recent_views)) {
            $recent_views = array();
        }
        
        // Ajouter l'horodatage actuel
        $recent_views[] = time();
        
        // Supprimer les vues de plus de 24 heures
        $one_day_ago = time() - (24 * 60 * 60);
        $recent_views = array_filter($recent_views, function($timestamp) use ($one_day_ago) {
            return $timestamp >= $one_day_ago;
        });
        
        // Mettre à jour les vues récentes
        update_post_meta($post_id, 'lejournaldesactus_recent_views', $recent_views);
    }
    
    /**
     * Calculer les articles tendance
     */
    public function calculate_trending_posts() {
        // Récupérer tous les articles publiés au cours des 7 derniers jours
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'date_query' => array(
                array(
                    'after' => '1 week ago',
                ),
            ),
        );
        
        $recent_posts = get_posts($args);
        $trending_scores = array();
        
        foreach ($recent_posts as $post) {
            // Récupérer les vues récentes
            $recent_views = get_post_meta($post->ID, 'lejournaldesactus_recent_views', true);
            $recent_views_count = is_array($recent_views) ? count($recent_views) : 0;
            
            // Récupérer le nombre de commentaires
            $comments_count = get_comments_number($post->ID);
            
            // Calculer le score de tendance
            // Formule : (vues récentes * 1) + (commentaires * 3)
            $trending_score = ($recent_views_count * 1) + ($comments_count * 3);
            
            // Stocker le score
            $trending_scores[$post->ID] = $trending_score;
        }
        
        // Trier par score décroissant
        arsort($trending_scores);
        
        // Limiter à 10 articles
        $trending_scores = array_slice($trending_scores, 0, 10, true);
        
        // Enregistrer les articles tendance
        update_option('lejournaldesactus_trending_posts', $trending_scores);
        
        return $trending_scores;
    }
    
    /**
     * Récupérer les articles tendance
     */
    public function get_trending_posts($count = 5) {
        $trending_scores = get_option('lejournaldesactus_trending_posts', array());
        
        // Si aucun article tendance n'est enregistré, les calculer
        if (empty($trending_scores)) {
            $trending_scores = $this->calculate_trending_posts();
        }
        
        // Limiter au nombre demandé
        $trending_scores = array_slice($trending_scores, 0, $count, true);
        
        // Récupérer les articles
        $trending_posts = array();
        foreach (array_keys($trending_scores) as $post_id) {
            $post = get_post($post_id);
            if ($post) {
                $trending_posts[] = $post;
            }
        }
        
        return $trending_posts;
    }
    
    /**
     * Shortcode pour afficher les articles tendance
     */
    public function trending_shortcode($atts) {
        $atts = shortcode_atts(array(
            'count' => 5,
            'title' => __('Articles Tendance', 'lejournaldesactus'),
            'layout' => 'list', // list, grid, compact
        ), $atts, 'lejournaldesactus_trending');
        
        $count = intval($atts['count']);
        $title = sanitize_text_field($atts['title']);
        $layout = sanitize_text_field($atts['layout']);
        
        $trending_posts = $this->get_trending_posts($count);
        
        if (empty($trending_posts)) {
            return '';
        }
        
        ob_start();
        ?>
        <div class="trending-posts-container trending-layout-<?php echo esc_attr($layout); ?>">
            <?php if (!empty($title)) : ?>
                <h3 class="trending-posts-title"><?php echo esc_html($title); ?></h3>
            <?php endif; ?>
            
            <div class="trending-posts-list">
                <?php foreach ($trending_posts as $index => $post) : ?>
                    <?php 
                    $post_thumbnail = get_the_post_thumbnail_url($post, 'medium');
                    $post_categories = get_the_category($post->ID);
                    $post_category = !empty($post_categories) ? $post_categories[0]->name : '';
                    $post_date = get_the_date('', $post);
                    $post_views = get_post_meta($post->ID, 'lejournaldesactus_post_views', true);
                    $post_views = $post_views ? $post_views : '0';
                    ?>
                    
                    <article class="trending-post-item">
                        <div class="trending-post-rank"><?php echo esc_html($index + 1); ?></div>
                        
                        <?php if ($layout !== 'compact' && $post_thumbnail) : ?>
                            <div class="trending-post-thumbnail">
                                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                    <img src="<?php echo esc_url($post_thumbnail); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>">
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="trending-post-content">
                            <?php if (!empty($post_category)) : ?>
                                <div class="trending-post-category"><?php echo esc_html($post_category); ?></div>
                            <?php endif; ?>
                            
                            <h4 class="trending-post-title">
                                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                    <?php echo esc_html(get_the_title($post)); ?>
                                </a>
                            </h4>
                            
                            <?php if ($layout === 'list') : ?>
                                <div class="trending-post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt($post), 20, '...'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="trending-post-meta">
                                <span class="trending-post-date"><?php echo esc_html($post_date); ?></span>
                                <span class="trending-post-views"><?php echo esc_html($post_views); ?> <?php _e('vues', 'lejournaldesactus'); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Enregistrer le widget des articles tendance
     */
    public function register_trending_widget() {
        register_widget('Lejournaldesactus_Trending_Widget');
    }
}

/**
 * Widget pour afficher les articles tendance
 */
class Lejournaldesactus_Trending_Widget extends WP_Widget {
    /**
     * Initialisation du widget
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_trending_widget',
            __('LJA - Articles Tendance', 'lejournaldesactus'),
            array(
                'description' => __('Affiche les articles les plus populaires du moment', 'lejournaldesactus'),
                'classname' => 'widget-trending-posts',
            )
        );
    }
    
    /**
     * Affichage du widget
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Articles Tendance', 'lejournaldesactus');
        $count = !empty($instance['count']) ? intval($instance['count']) : 5;
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'compact';
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        
        $trending = new Lejournaldesactus_Trending_Posts();
        $trending_posts = $trending->get_trending_posts($count);
        
        if (!empty($trending_posts)) {
            ?>
            <div class="trending-widget-list trending-layout-<?php echo esc_attr($layout); ?>">
                <?php foreach ($trending_posts as $index => $post) : ?>
                    <?php 
                    $post_thumbnail = get_the_post_thumbnail_url($post, 'thumbnail');
                    $post_views = get_post_meta($post->ID, 'lejournaldesactus_post_views', true);
                    $post_views = $post_views ? $post_views : '0';
                    ?>
                    
                    <article class="trending-widget-item">
                        <div class="trending-widget-rank"><?php echo esc_html($index + 1); ?></div>
                        
                        <?php if ($layout !== 'compact' && $post_thumbnail) : ?>
                            <div class="trending-widget-thumbnail">
                                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                    <img src="<?php echo esc_url($post_thumbnail); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>">
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="trending-widget-content">
                            <h4 class="trending-widget-title">
                                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                    <?php echo esc_html(get_the_title($post)); ?>
                                </a>
                            </h4>
                            
                            <div class="trending-widget-meta">
                                <span class="trending-widget-views"><?php echo esc_html($post_views); ?> <?php _e('vues', 'lejournaldesactus'); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php
        } else {
            echo '<p>' . __('Aucun article tendance pour le moment.', 'lejournaldesactus') . '</p>';
        }
        
        echo $args['after_widget'];
    }
    
    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Articles Tendance', 'lejournaldesactus');
        $count = !empty($instance['count']) ? intval($instance['count']) : 5;
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'compact';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Titre:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e('Nombre d\'articles à afficher:', 'lejournaldesactus'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="number" step="1" min="1" max="10" value="<?php echo esc_attr($count); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('layout')); ?>"><?php _e('Mise en page:', 'lejournaldesactus'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('layout')); ?>" name="<?php echo esc_attr($this->get_field_name('layout')); ?>">
                <option value="compact" <?php selected($layout, 'compact'); ?>><?php _e('Compact', 'lejournaldesactus'); ?></option>
                <option value="list" <?php selected($layout, 'list'); ?>><?php _e('Liste', 'lejournaldesactus'); ?></option>
                <option value="grid" <?php selected($layout, 'grid'); ?>><?php _e('Grille', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        <?php
    }
    
    /**
     * Sauvegarde des options du widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['count'] = (!empty($new_instance['count'])) ? intval($new_instance['count']) : 5;
        $instance['layout'] = (!empty($new_instance['layout'])) ? sanitize_text_field($new_instance['layout']) : 'compact';
        
        return $instance;
    }
}

// Initialiser la classe
$lejournaldesactus_trending_posts = new Lejournaldesactus_Trending_Posts();
