<?php
/**
 * Fonctionnalité d'articles liés intelligents
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Ajouter les options d'articles liés au Customizer
 */
function lejournaldesactus_related_posts_customize_register($wp_customize) {
    // Section pour les articles liés
    $wp_customize->add_section('lejournaldesactus_related_posts_section', array(
        'title'    => '🔗 ' . __('Articles Liés', 'lejournaldesactus'),
        'priority' => 160,
    ));
    
    // Option pour activer/désactiver les articles liés
    $wp_customize->add_setting('lejournaldesactus_show_related_posts', array(
        'default'           => true,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_show_related_posts', array(
        'label'       => '👀 ' . __('Afficher les articles liés', 'lejournaldesactus'),
        'description' => __('Afficher des suggestions d\'articles similaires à la fin de chaque article.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_related_posts_section',
        'type'        => 'checkbox',
    ));
    
    // Option pour le titre de la section
    $wp_customize->add_setting('lejournaldesactus_related_posts_title', array(
        'default'           => __('Articles similaires', 'lejournaldesactus'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('lejournaldesactus_related_posts_title', array(
        'label'    => '📝 ' . __('Titre de la section', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_related_posts_section',
        'type'     => 'text',
    ));
    
    // Option pour le nombre d'articles à afficher
    $wp_customize->add_setting('lejournaldesactus_related_posts_count', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('lejournaldesactus_related_posts_count', array(
        'label'       => '📊 ' . __('Nombre d\'articles à afficher', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_related_posts_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 9,
            'step' => 1,
        ),
    ));
    
    // Option pour la méthode de sélection
    $wp_customize->add_setting('lejournaldesactus_related_posts_method', array(
        'default'           => 'combined',
        'sanitize_callback' => 'lejournaldesactus_sanitize_select',
    ));
    
    $wp_customize->add_control('lejournaldesactus_related_posts_method', array(
        'label'    => '🔍 ' . __('Méthode de sélection', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_related_posts_section',
        'type'     => 'select',
        'choices'  => array(
            'categories' => __('Par catégories', 'lejournaldesactus'),
            'tags'       => __('Par tags', 'lejournaldesactus'),
            'combined'   => __('Catégories et tags', 'lejournaldesactus'),
            'content'    => __('Analyse du contenu', 'lejournaldesactus'),
        ),
    ));
    
    // Option pour afficher les images
    $wp_customize->add_setting('lejournaldesactus_related_posts_show_thumbnail', array(
        'default'           => true,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_related_posts_show_thumbnail', array(
        'label'    => '📸 ' . __('Afficher les images miniatures', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_related_posts_section',
        'type'     => 'checkbox',
    ));
    
    // Option pour afficher la date
    $wp_customize->add_setting('lejournaldesactus_related_posts_show_date', array(
        'default'           => true,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_related_posts_show_date', array(
        'label'    => '📆 ' . __('Afficher la date de publication', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_related_posts_section',
        'type'     => 'checkbox',
    ));
    
    // Option pour le style d'affichage
    $wp_customize->add_setting('lejournaldesactus_related_posts_style', array(
        'default'           => 'grid',
        'sanitize_callback' => 'lejournaldesactus_sanitize_select',
    ));
    
    $wp_customize->add_control('lejournaldesactus_related_posts_style', array(
        'label'    => '🎨 ' . __('Style d\'affichage', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_related_posts_section',
        'type'     => 'select',
        'choices'  => array(
            'grid'   => __('Grille', 'lejournaldesactus'),
            'list'   => __('Liste', 'lejournaldesactus'),
            'slider' => __('Carrousel', 'lejournaldesactus'),
        ),
    ));
}
add_action('customize_register', 'lejournaldesactus_related_posts_customize_register');

/**
 * Récupérer les articles liés
 */
function lejournaldesactus_get_related_posts($post_id = null, $count = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    if (!$count) {
        $count = get_theme_mod('lejournaldesactus_related_posts_count', 3);
    }
    
    $method = get_theme_mod('lejournaldesactus_related_posts_method', 'combined');
    $related_posts = array();
    
    switch ($method) {
        case 'categories':
            $related_posts = lejournaldesactus_get_related_by_categories($post_id, $count);
            break;
            
        case 'tags':
            $related_posts = lejournaldesactus_get_related_by_tags($post_id, $count);
            break;
            
        case 'content':
            $related_posts = lejournaldesactus_get_related_by_content($post_id, $count);
            break;
            
        case 'combined':
        default:
            // Essayer d'abord par tags et catégories combinés
            $related_posts = lejournaldesactus_get_related_by_combined($post_id, $count);
            
            // Si pas assez d'articles, compléter avec des articles par catégories
            if (count($related_posts) < $count) {
                $additional_posts = lejournaldesactus_get_related_by_categories($post_id, $count - count($related_posts), $related_posts);
                $related_posts = array_merge($related_posts, $additional_posts);
            }
            
            // Si toujours pas assez, compléter avec des articles récents
            if (count($related_posts) < $count) {
                $additional_posts = lejournaldesactus_get_recent_posts($count - count($related_posts), $related_posts);
                $related_posts = array_merge($related_posts, $additional_posts);
            }
            break;
    }
    
    return $related_posts;
}

/**
 * Récupérer les articles liés par catégories
 */
function lejournaldesactus_get_related_by_categories($post_id, $count, $exclude_ids = array()) {
    $categories = get_the_category($post_id);
    
    if (empty($categories)) {
        return array();
    }
    
    $category_ids = array();
    foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
    }
    
    $exclude_ids[] = $post_id; // Exclure l'article courant
    
    $args = array(
        'category__in'        => $category_ids,
        'post__not_in'        => $exclude_ids,
        'posts_per_page'      => $count,
        'ignore_sticky_posts' => 1,
        'orderby'             => 'rand',
    );
    
    $query = new WP_Query($args);
    
    return $query->posts;
}

/**
 * Récupérer les articles liés par tags
 */
function lejournaldesactus_get_related_by_tags($post_id, $count, $exclude_ids = array()) {
    $tags = get_the_tags($post_id);
    
    if (empty($tags)) {
        return array();
    }
    
    $tag_ids = array();
    foreach ($tags as $tag) {
        $tag_ids[] = $tag->term_id;
    }
    
    $exclude_ids[] = $post_id; // Exclure l'article courant
    
    $args = array(
        'tag__in'             => $tag_ids,
        'post__not_in'        => $exclude_ids,
        'posts_per_page'      => $count,
        'ignore_sticky_posts' => 1,
        'orderby'             => 'rand',
    );
    
    $query = new WP_Query($args);
    
    return $query->posts;
}

/**
 * Récupérer les articles liés par contenu
 */
function lejournaldesactus_get_related_by_content($post_id, $count, $exclude_ids = array()) {
    $post = get_post($post_id);
    
    if (empty($post)) {
        return array();
    }
    
    // Extraire les mots-clés du contenu
    $content = $post->post_title . ' ' . $post->post_content;
    $content = strip_tags($content);
    $content = strtolower($content);
    
    // Supprimer la ponctuation
    $content = preg_replace('/[^\p{L}\p{N}\s]/u', '', $content);
    
    // Diviser en mots
    $words = preg_split('/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
    
    // Mots à ignorer (stop words)
    $stop_words = array('le', 'la', 'les', 'un', 'une', 'des', 'et', 'ou', 'de', 'du', 'en', 'à', 'au', 'aux', 'ce', 'ces', 'cette', 'pour', 'par', 'sur', 'dans', 'avec', 'sans', 'qui', 'que', 'quoi', 'dont', 'où', 'comment', 'pourquoi', 'quand', 'est', 'sont', 'sera', 'seront', 'a', 'ont', 'avait', 'avaient', 'avoir', 'être', 'faire', 'fait', 'plus', 'moins', 'très', 'peu', 'beaucoup', 'trop', 'pas', 'ne', 'ni', 'mais', 'ou', 'donc', 'or', 'car', 'si', 'ainsi', 'alors', 'après', 'avant', 'pendant', 'depuis', 'jusqu', 'vers', 'chez', 'entre', 'parmi', 'selon', 'comme', 'même', 'aussi', 'autre', 'autres', 'certain', 'certains', 'certaine', 'certaines', 'tout', 'tous', 'toute', 'toutes', 'aucun', 'aucune', 'aucuns', 'aucunes', 'plusieurs', 'tel', 'tels', 'telle', 'telles');
    
    // Filtrer les mots vides et courts
    $keywords = array();
    foreach ($words as $word) {
        if (strlen($word) > 3 && !in_array($word, $stop_words)) {
            $keywords[] = $word;
        }
    }
    
    // Compter les occurrences
    $keyword_counts = array_count_values($keywords);
    
    // Trier par fréquence
    arsort($keyword_counts);
    
    // Prendre les 10 mots-clés les plus fréquents
    $top_keywords = array_slice($keyword_counts, 0, 10, true);
    $top_keywords = array_keys($top_keywords);
    
    if (empty($top_keywords)) {
        return array();
    }
    
    // Construire la requête de recherche
    $search_query = implode(' OR ', $top_keywords);
    
    $exclude_ids[] = $post_id; // Exclure l'article courant
    
    $args = array(
        's'                   => $search_query,
        'post__not_in'        => $exclude_ids,
        'posts_per_page'      => $count,
        'ignore_sticky_posts' => 1,
        'post_type'           => 'post',
        'post_status'         => 'publish',
    );
    
    $query = new WP_Query($args);
    
    return $query->posts;
}

/**
 * Récupérer les articles liés par tags et catégories combinés
 */
function lejournaldesactus_get_related_by_combined($post_id, $count, $exclude_ids = array()) {
    $categories = get_the_category($post_id);
    $tags = get_the_tags($post_id);
    
    if (empty($categories) && empty($tags)) {
        return array();
    }
    
    $category_ids = array();
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $category_ids[] = $category->term_id;
        }
    }
    
    $tag_ids = array();
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            $tag_ids[] = $tag->term_id;
        }
    }
    
    $exclude_ids[] = $post_id; // Exclure l'article courant
    
    $args = array(
        'posts_per_page'      => $count,
        'post__not_in'        => $exclude_ids,
        'ignore_sticky_posts' => 1,
        'orderby'             => 'rand',
        'tax_query'           => array(
            'relation'        => 'OR',
        ),
    );
    
    if (!empty($category_ids)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $category_ids,
        );
    }
    
    if (!empty($tag_ids)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => $tag_ids,
        );
    }
    
    $query = new WP_Query($args);
    
    return $query->posts;
}

/**
 * Récupérer les articles récents
 */
function lejournaldesactus_get_recent_posts($count, $exclude_ids = array()) {
    $args = array(
        'posts_per_page'      => $count,
        'post__not_in'        => $exclude_ids,
        'ignore_sticky_posts' => 1,
        'post_type'           => 'post',
        'post_status'         => 'publish',
    );
    
    $query = new WP_Query($args);
    
    return $query->posts;
}

/**
 * Afficher les articles liés
 */
function lejournaldesactus_display_related_posts() {
    // Vérifier si l'affichage des articles liés est activé
    if (!get_theme_mod('lejournaldesactus_show_related_posts', true)) {
        return;
    }
    
    // Ne pas afficher sur les pages
    if (!is_single()) {
        return;
    }
    
    $post_id = get_the_ID();
    $count = get_theme_mod('lejournaldesactus_related_posts_count', 3);
    $title = get_theme_mod('lejournaldesactus_related_posts_title', __('Articles similaires', 'lejournaldesactus'));
    $show_thumbnail = get_theme_mod('lejournaldesactus_related_posts_show_thumbnail', true);
    $show_date = get_theme_mod('lejournaldesactus_related_posts_show_date', true);
    $style = get_theme_mod('lejournaldesactus_related_posts_style', 'grid');
    
    $related_posts = lejournaldesactus_get_related_posts($post_id, $count);
    
    if (empty($related_posts)) {
        return;
    }
    
    // Déterminer la classe CSS en fonction du style
    $container_class = 'related-posts';
    $item_class = 'related-post-item';
    
    switch ($style) {
        case 'list':
            $container_class .= ' related-posts-list';
            $item_class .= ' related-post-item-list';
            break;
            
        case 'slider':
            $container_class .= ' related-posts-slider swiper';
            $item_class .= ' related-post-item-slider swiper-slide';
            break;
            
        case 'grid':
        default:
            $container_class .= ' related-posts-grid';
            $item_class .= ' related-post-item-grid';
            break;
    }
    
    // Déterminer la classe de la grille en fonction du nombre d'articles
    $grid_class = '';
    if ($style === 'grid') {
        switch ($count) {
            case 1:
                $grid_class = 'col-12';
                break;
            case 2:
                $grid_class = 'col-md-6';
                break;
            case 3:
                $grid_class = 'col-md-4';
                break;
            case 4:
                $grid_class = 'col-md-3';
                break;
            default:
                $grid_class = 'col-md-4';
                break;
        }
    }
    
    ?>
    <div class="related-posts-section">
        <h3 class="related-posts-title"><?php echo esc_html($title); ?></h3>
        
        <?php if ($style === 'slider') : ?>
        <div class="<?php echo esc_attr($container_class); ?>">
            <div class="swiper-wrapper">
        <?php elseif ($style === 'grid') : ?>
        <div class="<?php echo esc_attr($container_class); ?> row">
        <?php else : ?>
        <div class="<?php echo esc_attr($container_class); ?>">
        <?php endif; ?>
        
            <?php foreach ($related_posts as $related_post) : ?>
                <div class="<?php echo esc_attr($item_class); ?> <?php echo esc_attr($grid_class); ?>">
                    <article class="related-post">
                        <?php if ($show_thumbnail && has_post_thumbnail($related_post->ID)) : ?>
                        <div class="related-post-thumbnail">
                            <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>">
                                <?php echo get_the_post_thumbnail($related_post->ID, 'medium', array('class' => 'img-fluid')); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div class="related-post-content">
                            <h4 class="related-post-title">
                                <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>">
                                    <?php echo esc_html(get_the_title($related_post->ID)); ?>
                                </a>
                            </h4>
                            
                            <?php if ($show_date) : ?>
                            <div class="related-post-meta">
                                <span class="related-post-date">
                                    <i class="bi bi-calendar"></i>
                                    <?php echo esc_html(get_the_date('', $related_post->ID)); ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="related-post-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt($related_post->ID), 15, '...'); ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
            
        <?php if ($style === 'slider') : ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        <?php endif; ?>
        </div>
    </div>
    
    <?php if ($style === 'slider') : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.related-posts-slider', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    992: {
                        slidesPerView: <?php echo min(3, $count); ?>,
                    },
                },
                autoplay: {
                    delay: 5000,
                },
            });
        });
    </script>
    <?php endif; ?>
    
    <?php
}
add_action('lejournaldesactus_after_single_content', 'lejournaldesactus_display_related_posts');

/**
 * Ajouter un hook pour afficher les articles liés après le contenu de l'article
 */
function lejournaldesactus_add_related_posts_hook() {
    // Ne rien faire ici, nous utiliserons le hook directement dans single.php
}
add_action('wp_footer', 'lejournaldesactus_add_related_posts_hook');

/**
 * Fonction pour ajouter le hook dans le template single.php
 */
function lejournaldesactus_add_related_posts_to_single() {
    // Ne plus utiliser cette fonction car nous allons modifier single.php directement
}
add_action('wp_footer', 'lejournaldesactus_add_related_posts_to_single');

/**
 * Fonction AJAX pour récupérer le HTML des articles liés
 */
function lejournaldesactus_get_related_posts_html() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    
    if ($post_id) {
        // Démarrer la mise en tampon de sortie
        ob_start();
        
        // Afficher les articles liés
        lejournaldesactus_display_related_posts();
        
        // Récupérer le contenu du tampon
        $html = ob_get_clean();
        
        echo $html;
    }
    
    wp_die();
}
add_action('wp_ajax_lejournaldesactus_get_related_posts_html', 'lejournaldesactus_get_related_posts_html');
add_action('wp_ajax_nopriv_lejournaldesactus_get_related_posts_html', 'lejournaldesactus_get_related_posts_html');
