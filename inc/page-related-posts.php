<?php
/**
 * Fonctionnalité pour afficher des articles liés sur les pages
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour gérer les articles liés sur les pages
 */
class Lejournaldesactus_Page_Related_Posts {
    
    /**
     * Constructeur
     */
    public function __construct() {
        // Ajouter la metabox pour les pages
        add_action('add_meta_boxes', array($this, 'add_related_posts_metabox'));
        
        // Sauvegarder les données de la metabox
        add_action('save_post', array($this, 'save_related_posts_metabox'));
        
        // Ajouter le shortcode pour afficher les articles liés
        add_shortcode('lejournaldesactus_related_posts', array($this, 'related_posts_shortcode'));
        
        // Ajouter une action pour vérifier l'initialisation correcte
        add_action('admin_notices', array($this, 'check_metabox_initialization'));
    }
    
    /**
     * Vérifier l'initialisation correcte de la metabox
     */
    public function check_metabox_initialization() {
        global $pagenow;
        
        // Seulement sur l'écran d'édition de page
        if ($pagenow === 'post.php' && isset($_GET['post']) && get_post_type($_GET['post']) === 'page') {
            $post_id = intval($_GET['post']);
            
            // Vérifier si les données de la metabox sont présentes
            $show_related = get_post_meta($post_id, '_lejournaldesactus_show_page_related_posts', true);
            $method = get_post_meta($post_id, '_lejournaldesactus_page_related_posts_method', true);
            
            // Afficher un message si les données sont manquantes
            if (empty($show_related) && empty($method)) {
                echo '<div class="notice notice-warning is-dismissible">';
                echo '<p><strong>Diagnostic des articles liés :</strong> Aucune donnée de configuration trouvée pour cette page. Veuillez sauvegarder la page après avoir configuré les options des articles liés.</p>';
                echo '</div>';
            }
        }
    }
    
    /**
     * Ajouter la metabox pour les articles liés
     */
    public function add_related_posts_metabox() {
        add_meta_box(
            'lejournaldesactus_page_related_posts',
            __('Articles associés', 'lejournaldesactus'),
            array($this, 'render_related_posts_metabox'),
            'page',
            'normal',
            'high'
        );
    }
    
    /**
     * Afficher le contenu de la metabox
     */
    public function render_related_posts_metabox($post) {
        // Ajouter un nonce pour la vérification
        wp_nonce_field('lejournaldesactus_page_related_posts_nonce', 'lejournaldesactus_page_related_posts_nonce');
        
        // Récupérer les données sauvegardées
        $show_related = get_post_meta($post->ID, '_lejournaldesactus_show_page_related_posts', true);
        $related_method = get_post_meta($post->ID, '_lejournaldesactus_page_related_posts_method', true);
        $related_count = get_post_meta($post->ID, '_lejournaldesactus_page_related_posts_count', true);
        $related_category = get_post_meta($post->ID, '_lejournaldesactus_page_related_posts_category', true);
        $related_tag = get_post_meta($post->ID, '_lejournaldesactus_page_related_posts_tag', true);
        $related_title = get_post_meta($post->ID, '_lejournaldesactus_page_related_posts_title', true);
        $show_recent_if_empty = get_post_meta($post->ID, '_lejournaldesactus_page_related_posts_show_recent', true);
        
        // Valeurs par défaut
        if (empty($show_related)) $show_related = 'yes';
        if (empty($related_method)) $related_method = 'auto';
        if (empty($related_count)) $related_count = 6;        
        if (empty($related_title)) $related_title = '';

        if (empty($show_recent_if_empty)) $show_recent_if_empty = 'yes';
        
        // Récupérer toutes les catégories
        $categories = get_categories(array(
            'orderby' => 'name',
            'order'   => 'ASC',
            'hide_empty' => false,
        ));
        
        // Récupérer tous les tags
        $tags = get_tags(array(
            'orderby' => 'name',
            'order'   => 'ASC',
            'hide_empty' => false,
        ));
        
        ?>
        <div class="lejournaldesactus-metabox">
            <p>
                <label for="lejournaldesactus_show_page_related_posts">
                    <input type="checkbox" id="lejournaldesactus_show_page_related_posts" name="lejournaldesactus_show_page_related_posts" value="yes" <?php checked($show_related, 'yes'); ?> />
                    <?php _e('Afficher les articles liés sur cette page', 'lejournaldesactus'); ?>
                </label>
            </p>
            
            <p>
                <label for="lejournaldesactus_page_related_posts_title"><?php _e('Titre de la section :', 'lejournaldesactus'); ?></label>
                <input type="text" id="lejournaldesactus_page_related_posts_title" name="lejournaldesactus_page_related_posts_title" value="<?php echo esc_attr($related_title); ?>" class="widefat" />
            </p>
            
            <p>
                <label for="lejournaldesactus_page_related_posts_count"><?php _e('Nombre d\'articles à afficher :', 'lejournaldesactus'); ?></label>
                <input type="number" id="lejournaldesactus_page_related_posts_count" name="lejournaldesactus_page_related_posts_count" value="<?php echo esc_attr($related_count); ?>" min="1" max="12" step="1" />
            </p>
            
            <p>
                <label for="lejournaldesactus_page_related_posts_method"><?php _e('Méthode de sélection :', 'lejournaldesactus'); ?></label>
                <select id="lejournaldesactus_page_related_posts_method" name="lejournaldesactus_page_related_posts_method" class="widefat">
                    <option value="auto" <?php selected($related_method, 'auto'); ?>><?php _e('Automatique (basé sur le titre de la page)', 'lejournaldesactus'); ?></option>
                    <option value="category" <?php selected($related_method, 'category'); ?>><?php _e('Par catégorie spécifique', 'lejournaldesactus'); ?></option>
                    <option value="tag" <?php selected($related_method, 'tag'); ?>><?php _e('Par tag spécifique', 'lejournaldesactus'); ?></option>
                </select>
            </p>
            
            <div id="category-selector" style="<?php echo ($related_method == 'category') ? 'display:block;' : 'display:none;'; ?>">
                <p>
                    <label for="lejournaldesactus_page_related_posts_category"><?php _e('Sélectionner une catégorie :', 'lejournaldesactus'); ?></label>
                    <select id="lejournaldesactus_page_related_posts_category" name="lejournaldesactus_page_related_posts_category" class="widefat">
                        <option value=""><?php _e('-- Sélectionner --', 'lejournaldesactus'); ?></option>
                        <?php foreach ($categories as $category) : 
                            // Compter les articles dans cette catégorie
                            $cat_count = $category->count;
                        ?>
                            <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected($related_category, $category->term_id); ?>>
                                <?php echo esc_html($category->name); ?> (<?php echo $cat_count; ?> articles)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                
                <?php if (!empty($related_category)) : 
                    $selected_category = get_term($related_category, 'category');
                    if (!is_wp_error($selected_category) && !empty($selected_category)) :
                        $cat_posts = get_posts(array(
                            'category' => $related_category,
                            'numberposts' => 5,
                            'post_status' => 'publish'
                        ));
                ?>
                <div class="category-info" style="margin-top: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #e5e5e5;">
                    <p><strong><?php echo sprintf(__('Catégorie sélectionnée : %s', 'lejournaldesactus'), $selected_category->name); ?></strong></p>
                    <p><?php echo sprintf(__('Nombre d\'articles dans cette catégorie : %d', 'lejournaldesactus'), count($cat_posts)); ?></p>
                    
                    <?php if (!empty($cat_posts)) : ?>
                    <p><strong><?php _e('Articles récents dans cette catégorie :', 'lejournaldesactus'); ?></strong></p>
                    <ul style="margin-left: 20px; list-style: disc;">
                        <?php foreach ($cat_posts as $cat_post) : ?>
                        <li><?php echo esc_html($cat_post->post_title); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else : ?>
                    <p><em><?php _e('Aucun article publié dans cette catégorie.', 'lejournaldesactus'); ?></em></p>
                    <?php endif; ?>
                </div>
                <?php endif; endif; ?>
            </div>
            
            <div id="tag-selector" style="<?php echo ($related_method == 'tag') ? 'display:block;' : 'display:none;'; ?>">
                <p>
                    <label for="lejournaldesactus_page_related_posts_tag"><?php _e('Sélectionner un tag :', 'lejournaldesactus'); ?></label>
                    <select id="lejournaldesactus_page_related_posts_tag" name="lejournaldesactus_page_related_posts_tag" class="widefat">
                        <option value=""><?php _e('-- Sélectionner --', 'lejournaldesactus'); ?></option>
                        <?php foreach ($tags as $tag) : 
                            // Compter les articles avec ce tag
                            $tag_count = $tag->count;
                        ?>
                            <option value="<?php echo esc_attr($tag->term_id); ?>" <?php selected($related_tag, $tag->term_id); ?>>
                                <?php echo esc_html($tag->name); ?> (<?php echo $tag_count; ?> articles)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                
                <?php if (!empty($related_tag)) : 
                    $selected_tag = get_term($related_tag, 'post_tag');
                    if (!is_wp_error($selected_tag) && !empty($selected_tag)) :
                        $tag_posts = get_posts(array(
                            'tag_id' => $related_tag,
                            'numberposts' => 5,
                            'post_status' => 'publish'
                        ));
                ?>
                <div class="tag-info" style="margin-top: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #e5e5e5;">
                    <p><strong><?php echo sprintf(__('Tag sélectionné : %s', 'lejournaldesactus'), $selected_tag->name); ?></strong></p>
                    <p><?php echo sprintf(__('Nombre d\'articles avec ce tag : %d', 'lejournaldesactus'), count($tag_posts)); ?></p>
                    
                    <?php if (!empty($tag_posts)) : ?>
                    <p><strong><?php _e('Articles récents avec ce tag :', 'lejournaldesactus'); ?></strong></p>
                    <ul style="margin-left: 20px; list-style: disc;">
                        <?php foreach ($tag_posts as $tag_post) : ?>
                        <li><?php echo esc_html($tag_post->post_title); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else : ?>
                    <p><em><?php _e('Aucun article publié avec ce tag.', 'lejournaldesactus'); ?></em></p>
                    <?php endif; ?>
                </div>
                <?php endif; endif; ?>
            </div>
            
            <p>
                <label for="lejournaldesactus_page_related_posts_show_recent">
                    <input type="checkbox" id="lejournaldesactus_page_related_posts_show_recent" name="lejournaldesactus_page_related_posts_show_recent" value="yes" <?php checked($show_recent_if_empty, 'yes'); ?> />
                    <?php _e('Afficher les articles récents si aucun article lié n\'est trouvé', 'lejournaldesactus'); ?>
                </label>
            </p>
            
            <p class="description">
                <?php _e('Vous pouvez également utiliser le shortcode [lejournaldesactus_related_posts] dans le contenu de la page pour afficher les articles liés à un endroit spécifique.', 'lejournaldesactus'); ?>
            </p>
            
            <div class="shortcode-info" style="margin-top: 15px; padding: 10px; background: #f0f6fc; border: 1px solid #c5d5dd;">
                <p><strong><?php _e('Shortcodes disponibles :', 'lejournaldesactus'); ?></strong></p>
                <ul style="margin: 0; padding-left: 20px; list-style: disc;">
                    <li><code>[lejournaldesactus_related_posts]</code> - <?php _e('Affiche les articles liés selon la configuration ci-dessus', 'lejournaldesactus'); ?></li>
                    <li><code>[articles_recents nombre="6" titre="Articles récents" categorie=""]</code> - <?php _e('Affiche les articles récents avec options personnalisables', 'lejournaldesactus'); ?></li>
                </ul>
            </div>
        </div>
        
        <script>
            jQuery(document).ready(function($) {
                // Afficher/masquer les sélecteurs en fonction de la méthode choisie
                $('#lejournaldesactus_page_related_posts_method').on('change', function() {
                    var method = $(this).val();
                    
                    if (method === 'category') {
                        $('#category-selector').show();
                        $('#tag-selector').hide();
                    } else if (method === 'tag') {
                        $('#category-selector').hide();
                        $('#tag-selector').show();
                    } else {
                        $('#category-selector').hide();
                        $('#tag-selector').hide();
                    }
                });
                
                // Mettre à jour automatiquement la page lors du changement de catégorie ou tag
                $('#lejournaldesactus_page_related_posts_category, #lejournaldesactus_page_related_posts_tag').on('change', function() {
                    // Sauvegarder les données actuelles
                    var formData = $('#post').serialize();
                    
                    // Ajouter action=editpost pour simuler une sauvegarde
                    formData += '&action=editpost';
                    
                    // Envoyer la requête AJAX
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            // Recharger la page pour afficher les informations mises à jour
                            location.reload();
                        }
                    });
                });
            });
        </script>
        <?php
    }
    
    /**
     * Sauvegarder les données de la metabox
     */
    public function save_related_posts_metabox($post_id) {
        // Vérifier le nonce
        if (!isset($_POST['lejournaldesactus_page_related_posts_nonce']) || !wp_verify_nonce($_POST['lejournaldesactus_page_related_posts_nonce'], 'lejournaldesactus_page_related_posts_nonce')) {
            return;
        }
        
        // Vérifier les autorisations
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Sauvegarder les données
        $show_related = isset($_POST['lejournaldesactus_show_page_related_posts']) ? 'yes' : 'no';
        update_post_meta($post_id, '_lejournaldesactus_show_page_related_posts', $show_related);
        
        if (isset($_POST['lejournaldesactus_page_related_posts_method'])) {
            $method = sanitize_text_field($_POST['lejournaldesactus_page_related_posts_method']);
            update_post_meta($post_id, '_lejournaldesactus_page_related_posts_method', $method);
        }
        
        if (isset($_POST['lejournaldesactus_page_related_posts_count'])) {
            $count = intval($_POST['lejournaldesactus_page_related_posts_count']);
            if ($count < 1) $count = 1;
            if ($count > 12) $count = 12;
            update_post_meta($post_id, '_lejournaldesactus_page_related_posts_count', $count);
        }
        
        // Sauvegarde de la catégorie
        if (isset($_POST['lejournaldesactus_page_related_posts_category'])) {
            $category = intval($_POST['lejournaldesactus_page_related_posts_category']);
            update_post_meta($post_id, '_lejournaldesactus_page_related_posts_category', $category);
        } else if (isset($_POST['lejournaldesactus_page_related_posts_method']) && 
                  $_POST['lejournaldesactus_page_related_posts_method'] !== 'category') {
            // Si la méthode n'est pas par catégorie, réinitialiser la valeur
            update_post_meta($post_id, '_lejournaldesactus_page_related_posts_category', '');
        }
        
        // Sauvegarde du tag
        if (isset($_POST['lejournaldesactus_page_related_posts_tag'])) {
            $tag = intval($_POST['lejournaldesactus_page_related_posts_tag']);
            update_post_meta($post_id, '_lejournaldesactus_page_related_posts_tag', $tag);
        } else if (isset($_POST['lejournaldesactus_page_related_posts_method']) && 
                  $_POST['lejournaldesactus_page_related_posts_method'] !== 'tag') {
            // Si la méthode n'est pas par tag, réinitialiser la valeur
            update_post_meta($post_id, '_lejournaldesactus_page_related_posts_tag', '');
        }
        
        // Sauvegarde du titre (même s'il est vide)
        if (isset($_POST['lejournaldesactus_page_related_posts_title'])) {
            $title = sanitize_text_field($_POST['lejournaldesactus_page_related_posts_title']);
            update_post_meta($post_id, '_lejournaldesactus_page_related_posts_title', $title);
        }
        
        $show_recent = isset($_POST['lejournaldesactus_page_related_posts_show_recent']) ? 'yes' : 'no';
        update_post_meta($post_id, '_lejournaldesactus_page_related_posts_show_recent', $show_recent);
        
        // Vider le cache de la page
        clean_post_cache($post_id);
    }
    
    /**
     * Récupérer les articles liés pour une page
     */
    public function get_page_related_posts($post_id = null, $args = array()) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        // Récupérer les paramètres sauvegardés
        $show_related = get_post_meta($post_id, '_lejournaldesactus_show_page_related_posts', true);
        
        if ($show_related !== 'yes') {
            return new WP_Query();
        }
        
        $method = get_post_meta($post_id, '_lejournaldesactus_page_related_posts_method', true);
        $count = get_post_meta($post_id, '_lejournaldesactus_page_related_posts_count', true);
        $category_id = get_post_meta($post_id, '_lejournaldesactus_page_related_posts_category', true);
        $tag_id = get_post_meta($post_id, '_lejournaldesactus_page_related_posts_tag', true);
        
        // Valeurs par défaut
        if (empty($method)) $method = 'auto';
        if (empty($count) || !is_numeric($count)) $count = 6;
        
        // Fusionner avec les arguments passés
        $args = wp_parse_args($args, array(
            'count' => $count,
        ));
        
        // Préparer la requête
        $query_args = array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => $args['count'],
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true, // Amélioration des performances
            'cache_results'  => true,
        );
        
        // S'assurer que les IDs sont des entiers valides
        $category_id = intval($category_id);
        $tag_id = intval($tag_id);
        
        // Sélectionner les articles en fonction de la méthode
        if ($method === 'category' && $category_id > 0) {
            // Utiliser tax_query pour plus de précision
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                    'operator' => 'IN',
                ),
            );
        } elseif ($method === 'tag' && $tag_id > 0) {
            // Utiliser tax_query pour plus de précision
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $tag_id,
                    'operator' => 'IN',
                ),
            );
        } else {
            // Méthode automatique basée sur le titre de la page
            $page_title = get_the_title($post_id);
            $title_keywords = explode(' ', $page_title);
            $search_terms = array();
            
            foreach ($title_keywords as $keyword) {
                // Ignorer les mots courts ou les articles/prépositions
                if (strlen($keyword) > 3 && !in_array(strtolower($keyword), array('avec', 'pour', 'dans', 'cette', 'votre', 'notre', 'leurs', 'les', 'des', 'aux'))) {
                    $search_terms[] = $keyword;
                }
            }
            
            if (!empty($search_terms)) {
                $query_args['s'] = implode(' ', $search_terms);
            }
        }
        
        // Exclure la page actuelle des résultats
        $query_args['post__not_in'] = array($post_id);
        
        // Exécuter la requête
        $related_query = new WP_Query($query_args);
        
        return $related_query;
    }
    
    /**
     * Afficher les articles liés
     */
    public function display_page_related_posts($post_id = null, $args = array()) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        // Récupérer les paramètres sauvegardés
        $show_related = get_post_meta($post_id, '_lejournaldesactus_show_page_related_posts', true);
        
        if ($show_related !== 'yes') {
            return;
        }
        
        $title = get_post_meta($post_id, '_lejournaldesactus_page_related_posts_title', true);
        $show_recent = get_post_meta($post_id, '_lejournaldesactus_page_related_posts_show_recent', true);
        
        // Récupérer les articles liés
        $related_query = $this->get_page_related_posts($post_id, $args);
        
        // Afficher les articles
        echo '<div class="related-posts-section mt-5">';
        
        // Afficher le titre seulement s'il est défini
        if (!empty($title)) {
            echo '<h2 class="section-title mb-4">' . esc_html($title) . '</h2>';
        }
        
        if ($related_query->have_posts()) :
            $count = 0;
            echo '<div class="row">';
            
            // Boucle pour afficher les articles
            while ($related_query->have_posts()) : $related_query->the_post();
                $count++;
                
                if ($count === 1) {
                    // Premier article mis en avant (style hero)
                    echo '<div class="col-lg-12 mb-5">';
                    echo '<div class="hero-post">';
                    
                    echo '<div class="post-meta">';
                    lejournaldesactus_post_categories();
                    echo '</div>';
                    
                    echo '<h3 class="post-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
                    
                    // Afficher la date et l'auteur
                    lejournaldesactus_post_meta();
                    
                    if (has_post_thumbnail()) {
                        echo '<div class="post-img mt-3">';
                        echo '<a href="' . esc_url(get_permalink()) . '" class="img-link">';
                        the_post_thumbnail('large', array('class' => 'img-fluid'));
                        echo '</a>';
                        echo '</div>';
                    }
                    
                    echo '<div class="post-content mt-3">';
                    echo wp_trim_words(get_the_excerpt(), 30, '...');
                    echo '</div>';
                    
                    echo '<div class="d-flex align-items-center justify-content-between mt-4">';
                    echo '<a href="' . esc_url(get_permalink()) . '" class="read-more">' . esc_html__('Lire la suite', 'lejournaldesactus') . ' <i class="bi bi-arrow-right"></i></a>';
                    
                    echo '<div class="social-share">';
                    echo '<a href="#"><i class="bi bi-facebook"></i></a>';
                    echo '<a href="#"><i class="bi bi-twitter"></i></a>';
                    echo '<a href="#"><i class="bi bi-instagram"></i></a>';
                    echo '</div>';
                    
                    echo '</div>';
                    
                    echo '</div>';
                    echo '</div>';
                    
                    // Début de la section pour les autres articles
                    if ($related_query->post_count > 1) {
                        echo '<div class="col-lg-12">';
                        echo '<h3 class="mb-4">' . __('Autres articles sur ce sujet', 'lejournaldesactus') . '</h3>';
                        echo '<div class="row g-4">';
                    }
                } else {
                    // Autres articles en grille
                    echo '<div class="col-md-6 col-lg-4 mb-4">';
                    echo '<div class="post-box h-100">';
                    
                    if (has_post_thumbnail()) {
                        echo '<a href="' . esc_url(get_permalink()) . '" class="post-img">';
                        the_post_thumbnail('medium', array('class' => 'img-fluid'));
                        echo '</a>';
                    }
                    
                    echo '<div class="post-meta mt-3">';
                    lejournaldesactus_post_categories();
                    echo '</div>';
                    
                    echo '<h4 class="post-title mt-2"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h4>';
                    
                    // Afficher la date et l'auteur
                    lejournaldesactus_post_meta();
                    
                    echo '<div class="post-content mt-3">';
                    echo wp_trim_words(get_the_excerpt(), 15, '...');
                    echo '</div>';
                    
                    echo '<div class="d-flex align-items-center justify-content-between mt-4">';
                    echo '<a href="' . esc_url(get_permalink()) . '" class="read-more">' . esc_html__('Lire la suite', 'lejournaldesactus') . ' <i class="bi bi-arrow-right"></i></a>';
                    
                    echo '<div class="social-share">';
                    echo '<a href="#"><i class="bi bi-facebook"></i></a>';
                    echo '<a href="#"><i class="bi bi-twitter"></i></a>';
                    echo '<a href="#"><i class="bi bi-instagram"></i></a>';
                    echo '</div>';
                    
                    echo '</div>';
                    
                    echo '</div>';
                    echo '</div>';
                }
                
                // Fermer les divs si c'est le dernier article
                if ($count === $related_query->post_count && $count > 1) {
                    echo '</div>'; // Fermer .row
                    echo '</div>'; // Fermer .col-lg-12
                }
            endwhile;
            
            // Si un seul article a été trouvé, pas besoin de fermer les divs supplémentaires
            if ($count === 1) {
                echo '</div>'; // Fermer .row
            }
            
            wp_reset_postdata();
        elseif ($show_recent === 'yes') :
            // Afficher les articles récents si aucun article lié n'est trouvé
            $recent_query = new WP_Query(array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 6,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ));
            
            if ($recent_query->have_posts()) :
                $count = 0;
                echo '<div class="row">';
                
                // Boucle pour afficher les articles récents
                while ($recent_query->have_posts()) : $recent_query->the_post();
                    $count++;
                    
                    if ($count === 1) {
                        // Premier article mis en avant (style hero)
                        echo '<div class="col-lg-12 mb-5">';
                        echo '<div class="hero-post">';
                        
                        echo '<div class="post-meta">';
                        lejournaldesactus_post_categories();
                        echo '</div>';
                        
                        echo '<h3 class="post-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
                        
                        // Afficher la date et l'auteur
                        lejournaldesactus_post_meta();
                        
                        if (has_post_thumbnail()) {
                            echo '<div class="post-img mt-3">';
                            echo '<a href="' . esc_url(get_permalink()) . '" class="img-link">';
                            the_post_thumbnail('large', array('class' => 'img-fluid'));
                            echo '</a>';
                            echo '</div>';
                        }
                        
                        echo '<div class="post-content mt-3">';
                        echo wp_trim_words(get_the_excerpt(), 30, '...');
                        echo '</div>';
                        
                        echo '<div class="d-flex align-items-center justify-content-between mt-4">';
                        echo '<a href="' . esc_url(get_permalink()) . '" class="read-more">' . esc_html__('Lire la suite', 'lejournaldesactus') . ' <i class="bi bi-arrow-right"></i></a>';
                        
                        echo '<div class="social-share">';
                        echo '<a href="#"><i class="bi bi-facebook"></i></a>';
                        echo '<a href="#"><i class="bi bi-twitter"></i></a>';
                        echo '<a href="#"><i class="bi bi-instagram"></i></a>';
                        echo '</div>';
                        
                        echo '</div>';
                        
                        echo '</div>';
                        echo '</div>';
                        
                        // Début de la section pour les autres articles
                        if ($recent_query->post_count > 1) {
                            echo '<div class="col-lg-12">';
                            echo '<h3 class="mb-4">' . __('Articles récents', 'lejournaldesactus') . '</h3>';
                            echo '<div class="row g-4">';
                        }
                    } else {
                        // Autres articles en grille
                        echo '<div class="col-md-6 col-lg-4 mb-4">';
                        echo '<div class="post-box h-100">';
                        
                        if (has_post_thumbnail()) {
                            echo '<a href="' . esc_url(get_permalink()) . '" class="post-img">';
                            the_post_thumbnail('medium', array('class' => 'img-fluid'));
                            echo '</a>';
                        }
                        
                        echo '<div class="post-meta mt-3">';
                        lejournaldesactus_post_categories();
                        echo '</div>';
                        
                        echo '<h4 class="post-title mt-2"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h4>';
                        
                        // Afficher la date et l'auteur
                        lejournaldesactus_post_meta();
                        
                        echo '<div class="post-content mt-3">';
                        echo wp_trim_words(get_the_excerpt(), 15, '...');
                        echo '</div>';
                        
                        echo '<div class="d-flex align-items-center justify-content-between mt-4">';
                        echo '<a href="' . esc_url(get_permalink()) . '" class="read-more">' . esc_html__('Lire la suite', 'lejournaldesactus') . ' <i class="bi bi-arrow-right"></i></a>';
                        
                        echo '<div class="social-share">';
                        echo '<a href="#"><i class="bi bi-facebook"></i></a>';
                        echo '<a href="#"><i class="bi bi-twitter"></i></a>';
                        echo '<a href="#"><i class="bi bi-instagram"></i></a>';
                        echo '</div>';
                        
                        echo '</div>';
                        
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    // Fermer les divs si c'est le dernier article
                    if ($count === $recent_query->post_count && $count > 1) {
                        echo '</div>'; // Fermer .row
                        echo '</div>'; // Fermer .col-lg-12
                    }
                endwhile;
                
                // Si un seul article a été trouvé, pas besoin de fermer les divs supplémentaires
                if ($count === 1) {
                    echo '</div>'; // Fermer .row
                }
                
                wp_reset_postdata();
            else :
                echo '<div class="alert alert-info">';
                echo __('Aucun article publié trouvé.', 'lejournaldesactus');
                echo '</div>';
            endif;
        else :
            echo '<div class="alert alert-info">';
            echo __('Aucun article lié trouvé.', 'lejournaldesactus');
            echo '</div>';
        endif;
        
        echo '</div>';
    }
    
    /**
     * Shortcode pour afficher les articles liés
     */
    public function related_posts_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => null, // ID de la page pour laquelle afficher les articles liés
            'nombre' => null, // Nombre d'articles à afficher (remplace la valeur de la metabox)
        ), $atts, 'lejournaldesactus_related_posts');
        
        // Si aucun ID n'est spécifié, utiliser la page courante
        if (empty($atts['id'])) {
            $post_id = get_the_ID();
        } else {
            $post_id = intval($atts['id']);
        }
        
        // Préparer les arguments
        $args = array();
        if (!empty($atts['nombre'])) {
            $args['count'] = intval($atts['nombre']);
        }
        
        // Capturer la sortie
        ob_start();
        
        // Afficher les articles liés
        $this->display_page_related_posts($post_id, $args);
        
        // Récupérer le contenu
        return ob_get_clean();
    }
}

// Initialiser la classe
$lejournaldesactus_page_related_posts = new Lejournaldesactus_Page_Related_Posts();

// Fonction pour initialiser les hooks
function lejournaldesactus_init() {
    // Créer une instance de la classe
    global $lejournaldesactus_page_related_posts;
    if (!isset($lejournaldesactus_page_related_posts)) {
        $lejournaldesactus_page_related_posts = new Lejournaldesactus_Page_Related_Posts();
    }
    
    // Ajouter le shortcode pour les articles récents
    add_shortcode('articles_recents', 'lejournaldesactus_recent_posts_shortcode');
}
lejournaldesactus_init();

// Shortcode simple pour afficher les derniers articles
function lejournaldesactus_recent_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'nombre' => 6,
        'titre' => 'Articles récents',
        'categorie' => '',
    ), $atts, 'articles_recents');
    
    $query_args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => intval($atts['nombre']),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    
    // Si une catégorie est spécifiée
    if (!empty($atts['categorie'])) {
        // Vérifier si c'est un ID ou un nom de catégorie
        if (is_numeric($atts['categorie'])) {
            $query_args['cat'] = intval($atts['categorie']);
        } else {
            $category = get_term_by('name', $atts['categorie'], 'category');
            if ($category) {
                $query_args['cat'] = $category->term_id;
            }
        }
    }
    
    $recent_query = new WP_Query($query_args);
    
    ob_start();
    
    if ($recent_query->have_posts()) :
    ?>
    <div class="related-posts-section mt-5">
        <h2 class="section-title"><?php echo esc_html($atts['titre']); ?></h2>
        
        <div class="row g-4">
            <?php
            while ($recent_query->have_posts()) : $recent_query->the_post();
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="post-box h-100">
                    <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="post-img">
                        <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                    </a>
                    <?php endif; ?>
                    
                    <div class="post-meta mt-3">
                        <?php lejournaldesactus_post_categories(); ?>
                    </div>
                    
                    <h3 class="post-title mt-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    
                    <?php lejournaldesactus_post_meta(); ?>
                    
                    <div class="post-content mt-3">
                        <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <a href="<?php the_permalink(); ?>" class="read-more"><?php _e('Lire la suite', 'lejournaldesactus'); ?> <i class="bi bi-arrow-right"></i></a>
                        
                        <div class="social-share">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <?php
    else :
    ?>
    <div class="alert alert-info">
        <?php _e('Aucun article trouvé.', 'lejournaldesactus'); ?>
    </div>
    <?php
    endif;
    
    return ob_get_clean();
}

// Fonction pour initialiser les valeurs par défaut pour toutes les pages
function lejournaldesactus_initialize_related_posts_for_pages() {
    // Vérifier si l'action est demandée
    if (isset($_GET['initialize_related_posts']) && current_user_can('manage_options')) {
        // Récupérer toutes les pages
        $pages = get_posts(array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        $count = 0;
        
        // Parcourir chaque page
        foreach ($pages as $page) {
            // Vérifier si les données existent déjà
            $show_related = get_post_meta($page->ID, '_lejournaldesactus_show_page_related_posts', true);
            
            // Si les données n'existent pas, initialiser avec les valeurs par défaut
            if (empty($show_related)) {
                update_post_meta($page->ID, '_lejournaldesactus_show_page_related_posts', 'yes');
                update_post_meta($page->ID, '_lejournaldesactus_page_related_posts_method', 'auto');
                update_post_meta($page->ID, '_lejournaldesactus_page_related_posts_count', 6);
                update_post_meta($page->ID, '_lejournaldesactus_page_related_posts_title', '');
                update_post_meta($page->ID, '_lejournaldesactus_page_related_posts_show_recent', 'yes');
                $count++;
            }
        }
        
        // Ajouter un message d'admin
        add_action('admin_notices', function() use ($count) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>Initialisation des articles liés :</strong> ' . $count . ' pages ont été initialisées avec les valeurs par défaut.</p>';
            echo '</div>';
        });
        
        // Rediriger vers la liste des pages
        wp_redirect(admin_url('edit.php?post_type=page'));
        exit;
    }
}
add_action('admin_init', 'lejournaldesactus_initialize_related_posts_for_pages');

// Ajouter un lien dans le menu d'administration
function lejournaldesactus_add_related_posts_admin_menu() {
    add_management_page(
        __('Initialiser les articles liés', 'lejournaldesactus'),
        __('Initialiser les articles liés', 'lejournaldesactus'),
        'manage_options',
        'initialize-related-posts',
        'lejournaldesactus_related_posts_admin_page'
    );
}
add_action('admin_menu', 'lejournaldesactus_add_related_posts_admin_menu');

// Page d'administration pour initialiser les articles liés
function lejournaldesactus_related_posts_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Initialiser les articles liés pour toutes les pages', 'lejournaldesactus'); ?></h1>
        <p><?php _e('Cette action va initialiser les paramètres d\'articles liés pour toutes les pages qui n\'ont pas encore de configuration.', 'lejournaldesactus'); ?></p>
        <p><?php _e('Cela peut être utile si vous avez des problèmes avec l\'affichage des articles liés sur vos pages.', 'lejournaldesactus'); ?></p>
        
        <a href="<?php echo admin_url('tools.php?page=initialize-related-posts&initialize_related_posts=1'); ?>" class="button button-primary">
            <?php _e('Initialiser maintenant', 'lejournaldesactus'); ?>
        </a>
    </div>
    <?php
}
