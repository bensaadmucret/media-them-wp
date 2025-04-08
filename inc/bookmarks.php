<?php
/**
 * Système de Bookmarks/Favoris
 * 
 * Ce fichier contient les fonctions pour permettre aux utilisateurs
 * de sauvegarder des articles pour les lire plus tard.
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour gérer les bookmarks
 */
class Lejournaldesactus_Bookmarks {
    /**
     * Initialisation de la classe
     */
    public function __construct() {
        // Ajouter les actions AJAX pour les utilisateurs connectés et non connectés
        add_action('wp_ajax_lejournaldesactus_toggle_bookmark', array($this, 'toggle_bookmark'));
        add_action('wp_ajax_nopriv_lejournaldesactus_toggle_bookmark', array($this, 'toggle_bookmark_guest'));
        
        // Ajouter le bouton de bookmark aux articles
        add_filter('the_content', array($this, 'add_bookmark_button'));
        
        // Ajouter le shortcode pour afficher les bookmarks
        add_shortcode('lejournaldesactus_bookmarks', array($this, 'bookmarks_shortcode'));
        
        // Ajouter les scripts et styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Ajouter la page "Mes Favoris" au menu utilisateur
        add_filter('wp_nav_menu_items', array($this, 'add_bookmarks_menu_item'), 10, 2);
        
        // Créer la page de bookmarks lors de l'activation du thème
        add_action('after_switch_theme', array($this, 'create_bookmarks_page'));
    }
    
    /**
     * Ajouter les scripts et styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style('lejournaldesactus-bookmarks', LEJOURNALDESACTUS_THEME_URI . '/assets/css/bookmarks.css', array(), LEJOURNALDESACTUS_VERSION);
        wp_enqueue_script('lejournaldesactus-bookmarks', LEJOURNALDESACTUS_THEME_URI . '/assets/js/bookmarks.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
        
        wp_localize_script('lejournaldesactus-bookmarks', 'lejournaldesactusBookmarks', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lejournaldesactus_bookmark_nonce'),
            'loginUrl' => wp_login_url(get_permalink()),
            'loggedIn' => is_user_logged_in(),
            'bookmarkAdded' => __('Article ajouté aux favoris', 'lejournaldesactus'),
            'bookmarkRemoved' => __('Article retiré des favoris', 'lejournaldesactus'),
            'loginRequired' => __('Vous devez être connecté pour ajouter des favoris', 'lejournaldesactus'),
            'guestBookmarkAdded' => __('Article ajouté aux favoris temporaires', 'lejournaldesactus'),
            'guestBookmarkRemoved' => __('Article retiré des favoris temporaires', 'lejournaldesactus'),
        ));
    }
    
    /**
     * Ajouter le bouton de bookmark aux articles
     */
    public function add_bookmark_button($content) {
        if (is_single() && 'post' === get_post_type()) {
            $post_id = get_the_ID();
            $is_bookmarked = $this->is_post_bookmarked($post_id);
            
            $bookmark_button = '<div class="bookmark-button-container">';
            $bookmark_button .= '<button class="bookmark-button ' . ($is_bookmarked ? 'bookmarked' : '') . '" data-post-id="' . esc_attr($post_id) . '">';
            $bookmark_button .= '<i class="bi ' . ($is_bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark') . '"></i>';
            $bookmark_button .= '<span class="bookmark-text">' . ($is_bookmarked ? __('Enregistré', 'lejournaldesactus') : __('Enregistrer', 'lejournaldesactus')) . '</span>';
            $bookmark_button .= '</button>';
            $bookmark_button .= '</div>';
            
            return $content . $bookmark_button;
        }
        
        return $content;
    }
    
    /**
     * Vérifier si un article est dans les favoris
     */
    public function is_post_bookmarked($post_id) {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $bookmarks = get_user_meta($user_id, 'lejournaldesactus_bookmarks', true);
            
            if (!is_array($bookmarks)) {
                $bookmarks = array();
            }
            
            return in_array($post_id, $bookmarks);
        } else {
            // Pour les visiteurs, vérifier les cookies
            if (isset($_COOKIE['lejournaldesactus_guest_bookmarks'])) {
                $guest_bookmarks = json_decode(stripslashes($_COOKIE['lejournaldesactus_guest_bookmarks']), true);
                
                if (is_array($guest_bookmarks)) {
                    return in_array($post_id, $guest_bookmarks);
                }
            }
        }
        
        return false;
    }
    
    /**
     * Ajouter/Retirer un article des favoris (utilisateurs connectés)
     */
    public function toggle_bookmark() {
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'lejournaldesactus_bookmark_nonce')) {
            wp_send_json_error(__('Erreur de sécurité. Veuillez rafraîchir la page et réessayer.', 'lejournaldesactus'));
        }
        
        // Vérifier l'ID de l'article
        if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
            wp_send_json_error(__('ID d\'article invalide.', 'lejournaldesactus'));
        }
        
        $post_id = intval($_POST['post_id']);
        $user_id = get_current_user_id();
        
        // Récupérer les favoris actuels
        $bookmarks = get_user_meta($user_id, 'lejournaldesactus_bookmarks', true);
        
        if (!is_array($bookmarks)) {
            $bookmarks = array();
        }
        
        // Ajouter ou retirer l'article
        $is_bookmarked = in_array($post_id, $bookmarks);
        
        if ($is_bookmarked) {
            // Retirer l'article
            $bookmarks = array_diff($bookmarks, array($post_id));
            $message = __('Article retiré des favoris.', 'lejournaldesactus');
            $status = 'removed';
        } else {
            // Ajouter l'article
            $bookmarks[] = $post_id;
            $message = __('Article ajouté aux favoris.', 'lejournaldesactus');
            $status = 'added';
        }
        
        // Mettre à jour les favoris
        update_user_meta($user_id, 'lejournaldesactus_bookmarks', $bookmarks);
        
        wp_send_json_success(array(
            'message' => $message,
            'status' => $status,
            'count' => count($bookmarks),
        ));
    }
    
    /**
     * Ajouter/Retirer un article des favoris (visiteurs)
     */
    public function toggle_bookmark_guest() {
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'lejournaldesactus_bookmark_nonce')) {
            wp_send_json_error(__('Erreur de sécurité. Veuillez rafraîchir la page et réessayer.', 'lejournaldesactus'));
        }
        
        // Vérifier l'ID de l'article
        if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
            wp_send_json_error(__('ID d\'article invalide.', 'lejournaldesactus'));
        }
        
        $post_id = intval($_POST['post_id']);
        
        // Récupérer les favoris actuels depuis le cookie
        $bookmarks = array();
        
        if (isset($_COOKIE['lejournaldesactus_guest_bookmarks'])) {
            $bookmarks = json_decode(stripslashes($_COOKIE['lejournaldesactus_guest_bookmarks']), true);
            
            if (!is_array($bookmarks)) {
                $bookmarks = array();
            }
        }
        
        // Ajouter ou retirer l'article
        $is_bookmarked = in_array($post_id, $bookmarks);
        
        if ($is_bookmarked) {
            // Retirer l'article
            $bookmarks = array_diff($bookmarks, array($post_id));
            $message = __('Article retiré des favoris temporaires.', 'lejournaldesactus');
            $status = 'removed';
        } else {
            // Ajouter l'article
            $bookmarks[] = $post_id;
            $message = __('Article ajouté aux favoris temporaires.', 'lejournaldesactus');
            $status = 'added';
        }
        
        // Mettre à jour le cookie (expire dans 30 jours)
        setcookie('lejournaldesactus_guest_bookmarks', json_encode($bookmarks), time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        
        wp_send_json_success(array(
            'message' => $message,
            'status' => $status,
            'count' => count($bookmarks),
        ));
    }
    
    /**
     * Récupérer les articles favoris
     */
    public function get_bookmarked_posts($count = -1) {
        $bookmarks = array();
        
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $bookmarks = get_user_meta($user_id, 'lejournaldesactus_bookmarks', true);
        } else if (isset($_COOKIE['lejournaldesactus_guest_bookmarks'])) {
            $bookmarks = json_decode(stripslashes($_COOKIE['lejournaldesactus_guest_bookmarks']), true);
        }
        
        if (!is_array($bookmarks) || empty($bookmarks)) {
            return array();
        }
        
        // Récupérer les articles
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'post__in' => $bookmarks,
            'orderby' => 'post__in',
        );
        
        $query = new WP_Query($args);
        
        return $query->posts;
    }
    
    /**
     * Shortcode pour afficher les favoris
     */
    public function bookmarks_shortcode($atts) {
        $atts = shortcode_atts(array(
            'count' => -1,
            'title' => __('Mes Articles Favoris', 'lejournaldesactus'),
            'empty_message' => __('Vous n\'avez pas encore d\'articles favoris.', 'lejournaldesactus'),
            'layout' => 'list', // list, grid
        ), $atts, 'lejournaldesactus_bookmarks');
        
        $count = intval($atts['count']);
        $title = sanitize_text_field($atts['title']);
        $empty_message = sanitize_text_field($atts['empty_message']);
        $layout = sanitize_text_field($atts['layout']);
        
        $bookmarked_posts = $this->get_bookmarked_posts($count);
        
        ob_start();
        ?>
        <div class="bookmarks-container bookmarks-layout-<?php echo esc_attr($layout); ?>">
            <?php if (!empty($title)) : ?>
                <h2 class="bookmarks-title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            
            <?php if (empty($bookmarked_posts)) : ?>
                <div class="bookmarks-empty">
                    <p><?php echo esc_html($empty_message); ?></p>
                    <a href="<?php echo esc_url(home_url()); ?>" class="btn btn-primary"><?php _e('Parcourir les articles', 'lejournaldesactus'); ?></a>
                </div>
            <?php else : ?>
                <div class="bookmarks-list">
                    <?php foreach ($bookmarked_posts as $post) : ?>
                        <?php 
                        $post_thumbnail = get_the_post_thumbnail_url($post, 'medium');
                        $post_categories = get_the_category($post->ID);
                        $post_category = !empty($post_categories) ? $post_categories[0]->name : '';
                        $post_date = get_the_date('', $post);
                        ?>
                        
                        <article class="bookmark-item">
                            <?php if ($post_thumbnail) : ?>
                                <div class="bookmark-thumbnail">
                                    <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                        <img src="<?php echo esc_url($post_thumbnail); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>">
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="bookmark-content">
                                <?php if (!empty($post_category)) : ?>
                                    <div class="bookmark-category"><?php echo esc_html($post_category); ?></div>
                                <?php endif; ?>
                                
                                <h3 class="bookmark-title">
                                    <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                        <?php echo esc_html(get_the_title($post)); ?>
                                    </a>
                                </h3>
                                
                                <div class="bookmark-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt($post), 20, '...'); ?>
                                </div>
                                
                                <div class="bookmark-meta">
                                    <span class="bookmark-date"><?php echo esc_html($post_date); ?></span>
                                    <button class="bookmark-remove" data-post-id="<?php echo esc_attr($post->ID); ?>">
                                        <i class="bi bi-trash"></i> <?php _e('Retirer', 'lejournaldesactus'); ?>
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Ajouter un lien vers la page des favoris dans le menu
     */
    public function add_bookmarks_menu_item($items, $args) {
        if ($args->theme_location == 'primary') {
            $bookmarks_page_id = get_option('lejournaldesactus_bookmarks_page');
            
            if ($bookmarks_page_id) {
                $bookmarks_url = get_permalink($bookmarks_page_id);
                $bookmarks_count = 0;
                
                if (is_user_logged_in()) {
                    $user_id = get_current_user_id();
                    $bookmarks = get_user_meta($user_id, 'lejournaldesactus_bookmarks', true);
                    
                    if (is_array($bookmarks)) {
                        $bookmarks_count = count($bookmarks);
                    }
                } else if (isset($_COOKIE['lejournaldesactus_guest_bookmarks'])) {
                    $guest_bookmarks = json_decode(stripslashes($_COOKIE['lejournaldesactus_guest_bookmarks']), true);
                    
                    if (is_array($guest_bookmarks)) {
                        $bookmarks_count = count($guest_bookmarks);
                    }
                }
                
                $bookmark_item = '<li class="menu-item menu-item-bookmarks">';
                $bookmark_item .= '<a href="' . esc_url($bookmarks_url) . '">';
                $bookmark_item .= '<i class="bi bi-bookmark"></i> ' . __('Favoris', 'lejournaldesactus');
                
                if ($bookmarks_count > 0) {
                    $bookmark_item .= ' <span class="bookmark-count">' . $bookmarks_count . '</span>';
                }
                
                $bookmark_item .= '</a></li>';
                
                $items .= $bookmark_item;
            }
        }
        
        return $items;
    }
    
    /**
     * Créer la page des favoris lors de l'activation du thème
     */
    public function create_bookmarks_page() {
        $bookmarks_page_id = get_option('lejournaldesactus_bookmarks_page');
        
        if (!$bookmarks_page_id) {
            // Créer la page
            $page_data = array(
                'post_title' => __('Mes Favoris', 'lejournaldesactus'),
                'post_content' => '[lejournaldesactus_bookmarks]',
                'post_status' => 'publish',
                'post_type' => 'page',
            );
            
            $page_id = wp_insert_post($page_data);
            
            if (!is_wp_error($page_id)) {
                update_option('lejournaldesactus_bookmarks_page', $page_id);
            }
        }
    }
}

// Initialiser la classe
$lejournaldesactus_bookmarks = new Lejournaldesactus_Bookmarks();
