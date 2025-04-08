<?php
/**
 * Fonctionnalités d'optimisation SEO pour les articles
 *
 * @package LeJournalDesActus
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour gérer l'optimisation SEO des articles
 */
class LeJournalDesActus_SEO_Optimization {
    
    /**
     * Constructeur
     */
    public function __construct() {
        // Ajouter la meta box SEO
        add_action('add_meta_boxes', array($this, 'add_seo_meta_box'));
        
        // Sauvegarder les données SEO
        add_action('save_post', array($this, 'save_seo_meta_data'));
        
        // Ajouter les meta tags SEO dans le header
        add_action('wp_head', array($this, 'add_seo_meta_tags'), 1);
        
        // Modifier le titre des pages
        add_filter('document_title_parts', array($this, 'modify_document_title'));
        
        // Ajouter l'analyse SEO dans l'éditeur
        add_action('admin_enqueue_scripts', array($this, 'enqueue_seo_scripts'));
        
        // Ajouter les données structurées
        add_action('wp_footer', array($this, 'add_structured_data'));
    }
    
    /**
     * Ajouter la meta box SEO
     */
    public function add_seo_meta_box() {
        add_meta_box(
            'lejournaldesactus_seo_meta_box',
            __('Optimisation SEO', 'lejournaldesactus'),
            array($this, 'render_seo_meta_box'),
            'post',
            'normal',
            'high'
        );
    }
    
    /**
     * Afficher la meta box SEO
     */
    public function render_seo_meta_box($post) {
        wp_nonce_field('lejournaldesactus_save_seo_data', 'lejournaldesactus_seo_nonce');
        
        // Récupérer les valeurs existantes
        $seo_title = get_post_meta($post->ID, '_seo_title', true);
        $seo_description = get_post_meta($post->ID, '_seo_description', true);
        $seo_keywords = get_post_meta($post->ID, '_seo_keywords', true);
        $seo_focus_keyword = get_post_meta($post->ID, '_seo_focus_keyword', true);
        $seo_canonical_url = get_post_meta($post->ID, '_seo_canonical_url', true);
        $seo_no_index = get_post_meta($post->ID, '_seo_no_index', true);
        $seo_no_follow = get_post_meta($post->ID, '_seo_no_follow', true);
        
        // Calculer le score SEO
        $seo_score = $this->calculate_seo_score($post->ID);
        
        // Afficher les champs
        ?>
        <div class="lejournaldesactus-seo-meta-box">
            <div class="seo-score-wrapper">
                <div class="seo-score <?php echo esc_attr($this->get_score_class($seo_score)); ?>">
                    <span class="score-value"><?php echo esc_html($seo_score); ?></span>
                    <span class="score-label"><?php esc_html_e('Score SEO', 'lejournaldesactus'); ?></span>
                </div>
                <div class="seo-tips">
                    <?php $this->display_seo_tips($post->ID); ?>
                </div>
            </div>
            
            <div class="seo-preview">
                <h4><?php esc_html_e('Aperçu dans les résultats de recherche', 'lejournaldesactus'); ?></h4>
                <div class="search-preview">
                    <div class="preview-title" id="seo-preview-title">
                        <?php echo esc_html($seo_title ? $seo_title : get_the_title($post->ID)); ?>
                    </div>
                    <div class="preview-url">
                        <?php echo esc_url(get_permalink($post->ID)); ?>
                    </div>
                    <div class="preview-description" id="seo-preview-description">
                        <?php 
                        if ($seo_description) {
                            echo esc_html($seo_description);
                        } else {
                            $content = wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 30, '...');
                            echo esc_html($content);
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="seo-fields">
                <p>
                    <label for="seo_title"><?php esc_html_e('Titre SEO', 'lejournaldesactus'); ?></label>
                    <input type="text" id="seo_title" name="seo_title" value="<?php echo esc_attr($seo_title); ?>" class="widefat" maxlength="60">
                    <span class="description"><?php esc_html_e('Titre optimisé pour les moteurs de recherche (max. 60 caractères)', 'lejournaldesactus'); ?></span>
                    <span class="char-count" id="seo-title-count">0/60</span>
                </p>
                
                <p>
                    <label for="seo_description"><?php esc_html_e('Meta Description', 'lejournaldesactus'); ?></label>
                    <textarea id="seo_description" name="seo_description" rows="3" class="widefat" maxlength="160"><?php echo esc_textarea($seo_description); ?></textarea>
                    <span class="description"><?php esc_html_e('Description courte qui apparaîtra dans les résultats de recherche (max. 160 caractères)', 'lejournaldesactus'); ?></span>
                    <span class="char-count" id="seo-description-count">0/160</span>
                </p>
                
                <p>
                    <label for="seo_keywords"><?php esc_html_e('Mots-clés', 'lejournaldesactus'); ?></label>
                    <input type="text" id="seo_keywords" name="seo_keywords" value="<?php echo esc_attr($seo_keywords); ?>" class="widefat">
                    <span class="description"><?php esc_html_e('Mots-clés séparés par des virgules', 'lejournaldesactus'); ?></span>
                </p>
                
                <p>
                    <label for="seo_focus_keyword"><?php esc_html_e('Mot-clé principal', 'lejournaldesactus'); ?></label>
                    <input type="text" id="seo_focus_keyword" name="seo_focus_keyword" value="<?php echo esc_attr($seo_focus_keyword); ?>" class="widefat">
                    <span class="description"><?php esc_html_e('Le mot-clé principal sur lequel vous souhaitez vous positionner', 'lejournaldesactus'); ?></span>
                </p>
                
                <p>
                    <label for="seo_canonical_url"><?php esc_html_e('URL canonique', 'lejournaldesactus'); ?></label>
                    <input type="url" id="seo_canonical_url" name="seo_canonical_url" value="<?php echo esc_url($seo_canonical_url); ?>" class="widefat">
                    <span class="description"><?php esc_html_e('URL canonique si différente de l\'URL de l\'article', 'lejournaldesactus'); ?></span>
                </p>
                
                <div class="seo-checkboxes">
                    <p>
                        <input type="checkbox" id="seo_no_index" name="seo_no_index" value="1" <?php checked($seo_no_index, '1'); ?>>
                        <label for="seo_no_index"><?php esc_html_e('Ne pas indexer cet article', 'lejournaldesactus'); ?></label>
                    </p>
                    
                    <p>
                        <input type="checkbox" id="seo_no_follow" name="seo_no_follow" value="1" <?php checked($seo_no_follow, '1'); ?>>
                        <label for="seo_no_follow"><?php esc_html_e('Ne pas suivre les liens de cet article', 'lejournaldesactus'); ?></label>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Sauvegarder les données SEO
     */
    public function save_seo_meta_data($post_id) {
        // Vérifier le nonce
        if (!isset($_POST['lejournaldesactus_seo_nonce']) || !wp_verify_nonce($_POST['lejournaldesactus_seo_nonce'], 'lejournaldesactus_save_seo_data')) {
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
        if (isset($_POST['seo_title'])) {
            update_post_meta($post_id, '_seo_title', sanitize_text_field($_POST['seo_title']));
        }
        
        if (isset($_POST['seo_description'])) {
            update_post_meta($post_id, '_seo_description', sanitize_textarea_field($_POST['seo_description']));
        }
        
        if (isset($_POST['seo_keywords'])) {
            update_post_meta($post_id, '_seo_keywords', sanitize_text_field($_POST['seo_keywords']));
        }
        
        if (isset($_POST['seo_focus_keyword'])) {
            update_post_meta($post_id, '_seo_focus_keyword', sanitize_text_field($_POST['seo_focus_keyword']));
        }
        
        if (isset($_POST['seo_canonical_url'])) {
            update_post_meta($post_id, '_seo_canonical_url', esc_url_raw($_POST['seo_canonical_url']));
        }
        
        update_post_meta($post_id, '_seo_no_index', isset($_POST['seo_no_index']) ? '1' : '');
        update_post_meta($post_id, '_seo_no_follow', isset($_POST['seo_no_follow']) ? '1' : '');
        
        // Mettre à jour le score SEO
        $this->update_seo_score($post_id);
    }
    
    /**
     * Ajouter les meta tags SEO dans le header
     */
    public function add_seo_meta_tags() {
        global $post;
        
        if (!is_singular('post')) {
            return;
        }
        
        $post_id = $post->ID;
        
        // Récupérer les données SEO
        $seo_title = get_post_meta($post_id, '_seo_title', true);
        $seo_description = get_post_meta($post_id, '_seo_description', true);
        $seo_keywords = get_post_meta($post_id, '_seo_keywords', true);
        $seo_canonical_url = get_post_meta($post_id, '_seo_canonical_url', true);
        $seo_no_index = get_post_meta($post_id, '_seo_no_index', true);
        $seo_no_follow = get_post_meta($post_id, '_seo_no_follow', true);
        
        // Meta description
        if (!empty($seo_description)) {
            echo '<meta name="description" content="' . esc_attr($seo_description) . '" />' . "\n";
        }
        
        // Meta keywords
        if (!empty($seo_keywords)) {
            echo '<meta name="keywords" content="' . esc_attr($seo_keywords) . '" />' . "\n";
        }
        
        // Canonical URL
        if (!empty($seo_canonical_url)) {
            echo '<link rel="canonical" href="' . esc_url($seo_canonical_url) . '" />' . "\n";
        } else {
            echo '<link rel="canonical" href="' . esc_url(get_permalink($post_id)) . '" />' . "\n";
        }
        
        // Robots meta
        $robots = array();
        if ($seo_no_index) {
            $robots[] = 'noindex';
        } else {
            $robots[] = 'index';
        }
        
        if ($seo_no_follow) {
            $robots[] = 'nofollow';
        } else {
            $robots[] = 'follow';
        }
        
        echo '<meta name="robots" content="' . esc_attr(implode(',', $robots)) . '" />' . "\n";
        
        // Open Graph tags
        echo '<meta property="og:title" content="' . esc_attr($seo_title ? $seo_title : get_the_title($post_id)) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($seo_description ? $seo_description : wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 30, '...')) . '" />' . "\n";
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink($post_id)) . '" />' . "\n";
        
        // Featured image
        if (has_post_thumbnail($post_id)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
            echo '<meta property="og:image" content="' . esc_url($thumbnail_src[0]) . '" />' . "\n";
        }
        
        // Twitter Card
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($seo_title ? $seo_title : get_the_title($post_id)) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($seo_description ? $seo_description : wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 30, '...')) . '" />' . "\n";
        
        if (has_post_thumbnail($post_id)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
            echo '<meta name="twitter:image" content="' . esc_url($thumbnail_src[0]) . '" />' . "\n";
        }
    }
    
    /**
     * Modifier le titre des pages
     */
    public function modify_document_title($title_parts) {
        global $post;
        
        if (is_singular('post') && isset($post->ID)) {
            $seo_title = get_post_meta($post->ID, '_seo_title', true);
            
            if (!empty($seo_title)) {
                $title_parts['title'] = $seo_title;
            }
        }
        
        return $title_parts;
    }
    
    /**
     * Ajouter les scripts pour l'analyse SEO
     */
    public function enqueue_seo_scripts($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        global $post;
        
        if ($post->post_type !== 'post') {
            return;
        }
        
        wp_enqueue_style('lejournaldesactus-seo-styles', get_template_directory_uri() . '/assets/css/seo-admin.css', array(), '1.0.0');
        wp_enqueue_script('lejournaldesactus-seo-scripts', get_template_directory_uri() . '/assets/js/seo-admin.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('lejournaldesactus-seo-scripts', 'lejournaldesactusSEO', array(
            'content' => $post->post_content,
            'title' => get_the_title($post->ID),
            'permalink' => get_permalink($post->ID),
            'focusKeyword' => get_post_meta($post->ID, '_seo_focus_keyword', true),
        ));
    }
    
    /**
     * Ajouter les données structurées
     */
    public function add_structured_data() {
        if (!is_singular('post')) {
            return;
        }
        
        global $post;
        $post_id = $post->ID;
        
        // Récupérer les données de l'auteur
        $author_data = lejournaldesactus_get_custom_author($post_id);
        $author_name = $author_data ? $author_data['name'] : get_the_author_meta('display_name', $post->post_author);
        
        // Récupérer les catégories
        $categories = get_the_category($post_id);
        $category_names = array();
        
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category_names[] = $category->name;
            }
        }
        
        // Créer les données structurées
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title($post_id),
            'description' => get_post_meta($post_id, '_seo_description', true) ?: wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 30, '...'),
            'author' => array(
                '@type' => 'Person',
                'name' => $author_name
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_template_directory_uri() . '/assets/img/logo.png'
                )
            ),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'mainEntityOfPage' => get_permalink($post_id),
            'keywords' => implode(', ', $category_names),
        );
        
        // Ajouter l'image mise en avant
        if (has_post_thumbnail($post_id)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
            $structured_data['image'] = array(
                '@type' => 'ImageObject',
                'url' => $thumbnail_src[0],
                'width' => $thumbnail_src[1],
                'height' => $thumbnail_src[2]
            );
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($structured_data) . '</script>' . "\n";
    }
    
    /**
     * Calculer le score SEO
     */
    public function calculate_seo_score($post_id) {
        $score = 0;
        $post = get_post($post_id);
        
        if (!$post) {
            return $score;
        }
        
        $content = $post->post_content;
        $title = get_the_title($post_id);
        $seo_title = get_post_meta($post_id, '_seo_title', true);
        $seo_description = get_post_meta($post_id, '_seo_description', true);
        $seo_focus_keyword = get_post_meta($post_id, '_seo_focus_keyword', true);
        
        // Vérifier la longueur du contenu (min 300 mots)
        $word_count = str_word_count(strip_tags($content));
        if ($word_count >= 300) {
            $score += 10;
        } elseif ($word_count >= 200) {
            $score += 5;
        }
        
        // Vérifier si le titre SEO est défini
        if (!empty($seo_title)) {
            $score += 10;
            
            // Vérifier la longueur du titre SEO (entre 40 et 60 caractères)
            $title_length = strlen($seo_title);
            if ($title_length >= 40 && $title_length <= 60) {
                $score += 5;
            }
        }
        
        // Vérifier si la meta description est définie
        if (!empty($seo_description)) {
            $score += 10;
            
            // Vérifier la longueur de la meta description (entre 120 et 160 caractères)
            $description_length = strlen($seo_description);
            if ($description_length >= 120 && $description_length <= 160) {
                $score += 5;
            }
        }
        
        // Vérifier si le mot-clé principal est défini
        if (!empty($seo_focus_keyword)) {
            $score += 10;
            
            // Vérifier si le mot-clé principal est présent dans le titre
            if (stripos($title, $seo_focus_keyword) !== false || stripos($seo_title, $seo_focus_keyword) !== false) {
                $score += 5;
            }
            
            // Vérifier si le mot-clé principal est présent dans la meta description
            if (stripos($seo_description, $seo_focus_keyword) !== false) {
                $score += 5;
            }
            
            // Vérifier si le mot-clé principal est présent dans le contenu
            if (stripos($content, $seo_focus_keyword) !== false) {
                $score += 5;
            }
            
            // Vérifier la densité du mot-clé principal (idéalement entre 1% et 3%)
            $keyword_count = substr_count(strtolower($content), strtolower($seo_focus_keyword));
            $keyword_density = ($keyword_count / $word_count) * 100;
            
            if ($keyword_density >= 1 && $keyword_density <= 3) {
                $score += 5;
            }
        }
        
        // Vérifier si l'article a une image mise en avant
        if (has_post_thumbnail($post_id)) {
            $score += 10;
        }
        
        // Vérifier si l'article a des sous-titres (h2, h3)
        if (preg_match('/<h[23][^>]*>/', $content)) {
            $score += 10;
        }
        
        // Vérifier si l'article a des liens internes
        if (preg_match_all('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>/', $content, $matches)) {
            $internal_links = 0;
            $site_url = get_site_url();
            
            foreach ($matches[1] as $link) {
                if (strpos($link, $site_url) === 0 || strpos($link, '/') === 0) {
                    $internal_links++;
                }
            }
            
            if ($internal_links > 0) {
                $score += 10;
            }
        }
        
        // Vérifier si l'article a des liens externes
        if (preg_match_all('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>/', $content, $matches)) {
            $external_links = 0;
            $site_url = get_site_url();
            
            foreach ($matches[1] as $link) {
                if (strpos($link, 'http') === 0 && strpos($link, $site_url) !== 0) {
                    $external_links++;
                }
            }
            
            if ($external_links > 0) {
                $score += 5;
            }
        }
        
        // Mettre à jour le score SEO
        update_post_meta($post_id, '_seo_score', $score);
        
        return $score;
    }
    
    /**
     * Mettre à jour le score SEO
     */
    public function update_seo_score($post_id) {
        $score = $this->calculate_seo_score($post_id);
        update_post_meta($post_id, '_seo_score', $score);
        return $score;
    }
    
    /**
     * Obtenir la classe CSS en fonction du score SEO
     */
    public function get_score_class($score) {
        if ($score >= 80) {
            return 'good';
        } elseif ($score >= 50) {
            return 'average';
        } else {
            return 'poor';
        }
    }
    
    /**
     * Afficher les conseils SEO
     */
    public function display_seo_tips($post_id) {
        $post = get_post($post_id);
        $content = $post->post_content;
        $title = get_the_title($post_id);
        $seo_title = get_post_meta($post_id, '_seo_title', true);
        $seo_description = get_post_meta($post_id, '_seo_description', true);
        $seo_focus_keyword = get_post_meta($post_id, '_seo_focus_keyword', true);
        
        $tips = array();
        
        // Vérifier la longueur du contenu
        $word_count = str_word_count(strip_tags($content));
        if ($word_count < 300) {
            $tips[] = sprintf(
                __('Le contenu est trop court (%d mots). Essayez d\'atteindre au moins 300 mots.', 'lejournaldesactus'),
                $word_count
            );
        }
        
        // Vérifier le titre SEO
        if (empty($seo_title)) {
            $tips[] = __('Ajoutez un titre SEO optimisé.', 'lejournaldesactus');
        } elseif (strlen($seo_title) < 40 || strlen($seo_title) > 60) {
            $tips[] = sprintf(
                __('La longueur du titre SEO est de %d caractères. Essayez de le maintenir entre 40 et 60 caractères.', 'lejournaldesactus'),
                strlen($seo_title)
            );
        }
        
        // Vérifier la meta description
        if (empty($seo_description)) {
            $tips[] = __('Ajoutez une meta description optimisée.', 'lejournaldesactus');
        } elseif (strlen($seo_description) < 120 || strlen($seo_description) > 160) {
            $tips[] = sprintf(
                __('La longueur de la meta description est de %d caractères. Essayez de la maintenir entre 120 et 160 caractères.', 'lejournaldesactus'),
                strlen($seo_description)
            );
        }
        
        // Vérifier le mot-clé principal
        if (empty($seo_focus_keyword)) {
            $tips[] = __('Définissez un mot-clé principal pour cet article.', 'lejournaldesactus');
        } else {
            // Vérifier si le mot-clé principal est présent dans le titre
            if (stripos($title, $seo_focus_keyword) === false && stripos($seo_title, $seo_focus_keyword) === false) {
                $tips[] = __('Incluez le mot-clé principal dans le titre de l\'article.', 'lejournaldesactus');
            }
            
            // Vérifier si le mot-clé principal est présent dans la meta description
            if (stripos($seo_description, $seo_focus_keyword) === false) {
                $tips[] = __('Incluez le mot-clé principal dans la meta description.', 'lejournaldesactus');
            }
            
            // Vérifier si le mot-clé principal est présent dans le contenu
            if (stripos($content, $seo_focus_keyword) === false) {
                $tips[] = __('Incluez le mot-clé principal dans le contenu de l\'article.', 'lejournaldesactus');
            }
            
            // Vérifier la densité du mot-clé principal
            $keyword_count = substr_count(strtolower($content), strtolower($seo_focus_keyword));
            $keyword_density = ($keyword_count / $word_count) * 100;
            
            if ($keyword_density < 1) {
                $tips[] = __('La densité du mot-clé principal est trop faible. Essayez d\'augmenter sa fréquence dans le contenu.', 'lejournaldesactus');
            } elseif ($keyword_density > 3) {
                $tips[] = __('La densité du mot-clé principal est trop élevée. Évitez le bourrage de mots-clés.', 'lejournaldesactus');
            }
        }
        
        // Vérifier si l'article a une image mise en avant
        if (!has_post_thumbnail($post_id)) {
            $tips[] = __('Ajoutez une image mise en avant à l\'article.', 'lejournaldesactus');
        }
        
        // Afficher les conseils
        if (!empty($tips)) {
            echo '<ul class="seo-tips-list">';
            foreach ($tips as $tip) {
                echo '<li>' . esc_html($tip) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="no-tips">' . esc_html__('Votre article est bien optimisé pour le SEO !', 'lejournaldesactus') . '</p>';
        }
        
        return $tips;
    }
}

// Initialiser la classe d'optimisation SEO
$lejournaldesactus_seo = new LeJournalDesActus_SEO_Optimization();