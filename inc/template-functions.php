<?php
/**
 * Fonctions liées aux templates et à l'affichage du contenu
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Fonction pour afficher les catégories d'un article
 */
function lejournaldesactus_post_categories() {
    $categories = get_the_category();
    if (!empty($categories)) {
        echo '<div class="post-categories">';
        foreach ($categories as $category) {
            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
        }
        echo '</div>';
    }
}

/**
 * Fonction pour afficher les métadonnées d'un article
 */
function lejournaldesactus_post_meta() {
    echo '<div class="post-meta-details">';
    echo '<span class="post-date"><i class="bi bi-calendar"></i> ' . get_the_date() . '</span>';
    echo '<span class="post-author"><i class="bi bi-person"></i> ' . get_the_author() . '</span>';
    if (comments_open()) {
        echo '<span class="post-comments"><i class="bi bi-chat"></i> ' . get_comments_number() . '</span>';
    }
    echo '</div>';
}

/**
 * Fonction pour afficher l'auteur personnalisé dans les articles
 */
function lejournaldesactus_display_custom_author() {
    global $post;
    
    $author_data = lejournaldesactus_get_custom_author($post->ID);
    
    if (!$author_data) {
        // Afficher l'auteur WordPress par défaut
        lejournaldesactus_display_default_author();
        return;
    }
    
    ?>
    <div class="post-author">
        <a href="<?php echo esc_url($author_data['url']); ?>" class="author-link">
            <?php if ($author_data['thumbnail']) : ?>
                <img src="<?php echo esc_url($author_data['thumbnail']); ?>" alt="<?php echo esc_attr($author_data['name']); ?>" class="author-img">
            <?php endif; ?>
            <span class="author-name"><?php echo esc_html($author_data['name']); ?></span>
        </a>
        <?php if ($author_data['designation']) : ?>
            <span class="author-designation"><?php echo esc_html($author_data['designation']); ?></span>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Fonction pour afficher l'auteur WordPress par défaut
 */
function lejournaldesactus_display_default_author() {
    ?>
    <div class="post-author">
        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-link">
            <?php echo get_avatar(get_the_author_meta('ID'), 50, '', '', array('class' => 'author-img')); ?>
            <span class="author-name"><?php the_author(); ?></span>
        </a>
    </div>
    <?php
}

/**
 * Fonction pour récupérer les données d'un auteur personnalisé
 */
function lejournaldesactus_get_custom_author($post_id) {
    $author_id = get_post_meta($post_id, '_custom_author', true);
    
    if (!$author_id) {
        return false;
    }
    
    $author = get_post($author_id);
    
    if (!$author || $author->post_type !== 'custom_author') {
        return false;
    }
    
    $author_data = array(
        'id' => $author->ID,
        'name' => $author->post_title,
        'url' => get_permalink($author->ID),
        'designation' => get_post_meta($author->ID, '_author_designation', true),
        'bio_short' => get_post_meta($author->ID, '_author_bio_short', true),
        'articles_count' => get_post_meta($author->ID, '_author_articles_count', true),
        'awards' => get_post_meta($author->ID, '_author_awards', true),
        'followers' => get_post_meta($author->ID, '_author_followers', true),
        'expertise' => get_post_meta($author->ID, '_author_expertise', true),
        'twitter' => get_post_meta($author->ID, '_author_twitter', true),
        'facebook' => get_post_meta($author->ID, '_author_facebook', true),
        'instagram' => get_post_meta($author->ID, '_author_instagram', true),
        'linkedin' => get_post_meta($author->ID, '_author_linkedin', true),
        'thumbnail' => get_the_post_thumbnail_url($author->ID, 'full'),
    );
    
    return $author_data;
}

/**
 * Fonction pour afficher les articles d'un auteur spécifique
 */
function lejournaldesactus_get_author_posts($author_id, $limit = 4) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'meta_key' => '_custom_author',
        'meta_value' => $author_id,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    return get_posts($args);
}
