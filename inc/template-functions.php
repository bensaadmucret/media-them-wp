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
    global $post;
    $author_id = $post->post_author;
    
    echo '<div class="post-meta-details">';
    echo '<span class="post-date"><i class="bi bi-calendar"></i> ' . get_the_date() . '</span>';
    
    // Vérifier si l'auteur a un profil personnalisé lié
    $linked_profile_id = get_user_meta($author_id, '_linked_author_profile', true);
    $author_name = get_the_author();
    
    if ($linked_profile_id) {
        // Utiliser le profil d'auteur personnalisé
        $profile = get_post($linked_profile_id);
        if ($profile && $profile->post_type === 'custom_author') {
            $author_name = $profile->post_title;
        }
    } else {
        // Vérifier si le profil d'auteur a un utilisateur lié (autre direction)
        $args = array(
            'post_type' => 'custom_author',
            'posts_per_page' => 1,
            'meta_key' => '_linked_user',
            'meta_value' => $author_id,
        );
        
        $linked_profiles = get_posts($args);
        
        if (!empty($linked_profiles)) {
            $profile = $linked_profiles[0];
            $author_name = $profile->post_title;
        }
    }
    
    echo '<span class="post-author"><i class="bi bi-person"></i> ' . esc_html($author_name) . '</span>';
    
    if (comments_open()) {
        echo '<span class="post-comments"><i class="bi bi-chat"></i> ' . get_comments_number() . '</span>';
    }
    echo '</div>';
}

/**
 * Fonction pour afficher l'auteur WordPress par défaut
 */
function lejournaldesactus_display_default_author() {
    global $post;
    $author_id = $post->post_author;
    
    // Vérifier si l'auteur a un profil personnalisé lié
    $linked_profile_id = get_user_meta($author_id, '_linked_author_profile', true);
    
    if ($linked_profile_id) {
        // Utiliser le profil d'auteur personnalisé
        $profile = get_post($linked_profile_id);
        // Vérifier si c'est un profil d'auteur (custom_author)
        if ($profile && $profile->post_type === 'custom_author') {
            // Récupérer les métadonnées du profil
            $author_name = $profile->post_title;
            $author_url = get_permalink($profile->ID);
            
            ?>
            <div class="post-author">
                <a href="<?php echo esc_url($author_url); ?>" class="author-link">
                    <span class="author-name"><?php echo esc_html($author_name); ?></span>
                </a>
            </div>
            <?php
            return;
        }
    }
    
    // Vérifier si le profil d'auteur a un utilisateur lié (autre direction)
    $args = array(
        'post_type' => 'custom_author',
        'posts_per_page' => 1,
        'meta_key' => '_linked_user',
        'meta_value' => $author_id,
    );
    
    $linked_profiles = get_posts($args);
    
    if (!empty($linked_profiles)) {
        $profile = $linked_profiles[0];
        // Récupérer les métadonnées du profil
        $author_name = $profile->post_title;
        $author_url = get_permalink($profile->ID);
        
        ?>
        <div class="post-author">
            <a href="<?php echo esc_url($author_url); ?>" class="author-link">
                <span class="author-name"><?php echo esc_html($author_name); ?></span>
            </a>
        </div>
        <?php
        return;
    }
    
    // Sinon, afficher l'auteur WordPress standard
    ?>
    <div class="post-author">
        <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="author-link">
            <span class="author-name"><?php the_author(); ?></span>
        </a>
    </div>
    <?php
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
