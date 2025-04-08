<?php
/**
 * Fonctions liées aux auteurs personnalisés
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Créer le Custom Post Type Auteur
 */
function lejournaldesactus_register_author_post_type() {
    $labels = array(
        'name'                  => _x('Auteurs', 'Post type general name', 'lejournaldesactus'),
        'singular_name'         => _x('Auteur', 'Post type singular name', 'lejournaldesactus'),
        'menu_name'             => _x('Auteurs', 'Admin Menu text', 'lejournaldesactus'),
        'name_admin_bar'        => _x('Auteur', 'Add New on Toolbar', 'lejournaldesactus'),
        'add_new'               => __('Ajouter', 'lejournaldesactus'),
        'add_new_item'          => __('Ajouter un nouvel auteur', 'lejournaldesactus'),
        'new_item'              => __('Nouvel auteur', 'lejournaldesactus'),
        'edit_item'             => __('Modifier l\'auteur', 'lejournaldesactus'),
        'view_item'             => __('Voir l\'auteur', 'lejournaldesactus'),
        'all_items'             => __('Tous les auteurs', 'lejournaldesactus'),
        'search_items'          => __('Rechercher des auteurs', 'lejournaldesactus'),
        'parent_item_colon'     => __('Auteur parent :', 'lejournaldesactus'),
        'not_found'             => __('Aucun auteur trouvé.', 'lejournaldesactus'),
        'not_found_in_trash'    => __('Aucun auteur trouvé dans la corbeille.', 'lejournaldesactus'),
        'featured_image'        => _x('Photo de l\'auteur', 'Overrides the "Featured Image" phrase', 'lejournaldesactus'),
        'set_featured_image'    => _x('Définir la photo de l\'auteur', 'Overrides the "Set featured image" phrase', 'lejournaldesactus'),
        'remove_featured_image' => _x('Supprimer la photo de l\'auteur', 'Overrides the "Remove featured image" phrase', 'lejournaldesactus'),
        'use_featured_image'    => _x('Utiliser comme photo de l\'auteur', 'Overrides the "Use as featured image" phrase', 'lejournaldesactus'),
        'archives'              => _x('Archives des auteurs', 'The post type archive label used in nav menus', 'lejournaldesactus'),
        'insert_into_item'      => _x('Insérer dans l\'auteur', 'Overrides the "Insert into post" phrase', 'lejournaldesactus'),
        'uploaded_to_this_item' => _x('Téléversé pour cet auteur', 'Overrides the "Uploaded to this post" phrase', 'lejournaldesactus'),
        'filter_items_list'     => _x('Filtrer la liste des auteurs', 'Screen reader text for the filter links heading on the post type listing screen', 'lejournaldesactus'),
        'items_list_navigation' => _x('Navigation de la liste des auteurs', 'Screen reader text for the pagination heading on the post type listing screen', 'lejournaldesactus'),
        'items_list'            => _x('Liste des auteurs', 'Screen reader text for the items list heading on the post type listing screen', 'lejournaldesactus'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'author'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-users',
        'supports'           => array('title', 'editor', 'thumbnail'),
    );

    register_post_type('author', $args);
}
add_action('init', 'lejournaldesactus_register_author_post_type');

/**
 * Ajouter les métadonnées personnalisées pour les auteurs
 */
function lejournaldesactus_add_author_meta_boxes() {
    add_meta_box(
        'author_details',
        __('Détails de l\'auteur', 'lejournaldesactus'),
        'lejournaldesactus_author_details_callback',
        'author',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'lejournaldesactus_add_author_meta_boxes');

/**
 * Callback pour afficher les champs personnalisés des auteurs
 */
function lejournaldesactus_author_details_callback($post) {
    // Ajouter un nonce pour la vérification
    wp_nonce_field('lejournaldesactus_save_author_data', 'lejournaldesactus_author_nonce');
    
    // Récupérer les valeurs existantes
    $designation = get_post_meta($post->ID, '_author_designation', true);
    $bio_short = get_post_meta($post->ID, '_author_bio_short', true);
    $articles_count = get_post_meta($post->ID, '_author_articles_count', true);
    $awards = get_post_meta($post->ID, '_author_awards', true);
    $followers = get_post_meta($post->ID, '_author_followers', true);
    $expertise = get_post_meta($post->ID, '_author_expertise', true);
    $twitter = get_post_meta($post->ID, '_author_twitter', true);
    $facebook = get_post_meta($post->ID, '_author_facebook', true);
    $instagram = get_post_meta($post->ID, '_author_instagram', true);
    $linkedin = get_post_meta($post->ID, '_author_linkedin', true);
    
    // Afficher les champs
    ?>
    <style>
        .author-meta-field {
            margin-bottom: 15px;
        }
        .author-meta-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .author-meta-field input[type="text"],
        .author-meta-field textarea {
            width: 100%;
        }
        .author-meta-section {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .author-meta-section h3 {
            margin-top: 0;
        }
    </style>
    
    <div class="author-meta-section">
        <h3><?php _e('Informations professionnelles', 'lejournaldesactus'); ?></h3>
        
        <div class="author-meta-field">
            <label for="author_designation"><?php _e('Fonction / Titre', 'lejournaldesactus'); ?></label>
            <input type="text" id="author_designation" name="author_designation" value="<?php echo esc_attr($designation); ?>" />
            <p class="description"><?php _e('Ex: Journaliste, Rédacteur en chef, etc.', 'lejournaldesactus'); ?></p>
        </div>
        
        <div class="author-meta-field">
            <label for="author_bio_short"><?php _e('Biographie courte', 'lejournaldesactus'); ?></label>
            <textarea id="author_bio_short" name="author_bio_short" rows="3"><?php echo esc_textarea($bio_short); ?></textarea>
            <p class="description"><?php _e('Une courte description qui apparaîtra dans les listes d\'articles.', 'lejournaldesactus'); ?></p>
        </div>
        
        <div class="author-meta-field">
            <label for="author_expertise"><?php _e('Domaines d\'expertise', 'lejournaldesactus'); ?></label>
            <input type="text" id="author_expertise" name="author_expertise" value="<?php echo esc_attr($expertise); ?>" />
            <p class="description"><?php _e('Ex: Politique, Économie, Sports, etc. (séparés par des virgules)', 'lejournaldesactus'); ?></p>
        </div>
    </div>
    
    <div class="author-meta-section">
        <h3><?php _e('Statistiques', 'lejournaldesactus'); ?></h3>
        
        <div class="author-meta-field">
            <label for="author_articles_count"><?php _e('Nombre d\'articles', 'lejournaldesactus'); ?></label>
            <input type="number" id="author_articles_count" name="author_articles_count" value="<?php echo esc_attr($articles_count); ?>" />
            <p class="description"><?php _e('Ce champ est mis à jour automatiquement.', 'lejournaldesactus'); ?></p>
        </div>
        
        <div class="author-meta-field">
            <label for="author_awards"><?php _e('Récompenses', 'lejournaldesactus'); ?></label>
            <input type="text" id="author_awards" name="author_awards" value="<?php echo esc_attr($awards); ?>" />
            <p class="description"><?php _e('Nombre de prix ou distinctions reçus.', 'lejournaldesactus'); ?></p>
        </div>
        
        <div class="author-meta-field">
            <label for="author_followers"><?php _e('Abonnés', 'lejournaldesactus'); ?></label>
            <input type="text" id="author_followers" name="author_followers" value="<?php echo esc_attr($followers); ?>" />
            <p class="description"><?php _e('Nombre total d\'abonnés sur les réseaux sociaux.', 'lejournaldesactus'); ?></p>
        </div>
    </div>
    
    <div class="author-meta-section">
        <h3><?php _e('Réseaux sociaux', 'lejournaldesactus'); ?></h3>
        
        <div class="author-meta-field">
            <label for="author_twitter"><?php _e('Twitter', 'lejournaldesactus'); ?></label>
            <input type="url" id="author_twitter" name="author_twitter" value="<?php echo esc_url($twitter); ?>" />
            <p class="description"><?php _e('URL complète du profil Twitter.', 'lejournaldesactus'); ?></p>
        </div>
        
        <div class="author-meta-field">
            <label for="author_facebook"><?php _e('Facebook', 'lejournaldesactus'); ?></label>
            <input type="url" id="author_facebook" name="author_facebook" value="<?php echo esc_url($facebook); ?>" />
            <p class="description"><?php _e('URL complète du profil Facebook.', 'lejournaldesactus'); ?></p>
        </div>
        
        <div class="author-meta-field">
            <label for="author_instagram"><?php _e('Instagram', 'lejournaldesactus'); ?></label>
            <input type="url" id="author_instagram" name="author_instagram" value="<?php echo esc_url($instagram); ?>" />
            <p class="description"><?php _e('URL complète du profil Instagram.', 'lejournaldesactus'); ?></p>
        </div>
        
        <div class="author-meta-field">
            <label for="author_linkedin"><?php _e('LinkedIn', 'lejournaldesactus'); ?></label>
            <input type="url" id="author_linkedin" name="author_linkedin" value="<?php echo esc_url($linkedin); ?>" />
            <p class="description"><?php _e('URL complète du profil LinkedIn.', 'lejournaldesactus'); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Sauvegarder les métadonnées des auteurs
 */
function lejournaldesactus_save_author_data($post_id) {
    // Vérifier si nous devons sauvegarder
    if (!isset($_POST['lejournaldesactus_author_nonce'])) {
        return;
    }
    
    // Vérifier si le nonce est valide
    if (!wp_verify_nonce($_POST['lejournaldesactus_author_nonce'], 'lejournaldesactus_save_author_data')) {
        return;
    }
    
    // Vérifier si c'est une sauvegarde automatique
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Vérifier les permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Sauvegarder les données
    if (isset($_POST['author_designation'])) {
        update_post_meta($post_id, '_author_designation', sanitize_text_field($_POST['author_designation']));
    }
    
    if (isset($_POST['author_bio_short'])) {
        update_post_meta($post_id, '_author_bio_short', sanitize_textarea_field($_POST['author_bio_short']));
    }
    
    if (isset($_POST['author_expertise'])) {
        update_post_meta($post_id, '_author_expertise', sanitize_text_field($_POST['author_expertise']));
    }
    
    if (isset($_POST['author_articles_count'])) {
        update_post_meta($post_id, '_author_articles_count', absint($_POST['author_articles_count']));
    }
    
    if (isset($_POST['author_awards'])) {
        update_post_meta($post_id, '_author_awards', sanitize_text_field($_POST['author_awards']));
    }
    
    if (isset($_POST['author_followers'])) {
        update_post_meta($post_id, '_author_followers', sanitize_text_field($_POST['author_followers']));
    }
    
    if (isset($_POST['author_twitter'])) {
        update_post_meta($post_id, '_author_twitter', esc_url_raw($_POST['author_twitter']));
    }
    
    if (isset($_POST['author_facebook'])) {
        update_post_meta($post_id, '_author_facebook', esc_url_raw($_POST['author_facebook']));
    }
    
    if (isset($_POST['author_instagram'])) {
        update_post_meta($post_id, '_author_instagram', esc_url_raw($_POST['author_instagram']));
    }
    
    if (isset($_POST['author_linkedin'])) {
        update_post_meta($post_id, '_author_linkedin', esc_url_raw($_POST['author_linkedin']));
    }
}
add_action('save_post_author', 'lejournaldesactus_save_author_data');

/**
 * Ajouter un champ pour sélectionner un auteur personnalisé dans les articles
 */
function lejournaldesactus_add_post_author_meta_box() {
    add_meta_box(
        'post_author',
        __('Auteur personnalisé', 'lejournaldesactus'),
        'lejournaldesactus_post_author_callback',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'lejournaldesactus_add_post_author_meta_box');

/**
 * Callback pour afficher le sélecteur d'auteur personnalisé
 */
function lejournaldesactus_post_author_callback($post) {
    // Ajouter un nonce pour la vérification
    wp_nonce_field('lejournaldesactus_save_post_author', 'lejournaldesactus_post_author_nonce');
    
    // Récupérer l'auteur sélectionné
    $selected_author = get_post_meta($post->ID, '_custom_author', true);
    
    // Récupérer tous les auteurs
    $authors = get_posts(array(
        'post_type' => 'author',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ));
    
    // Afficher le sélecteur
    ?>
    <p>
        <label for="custom_author"><?php _e('Sélectionner un auteur personnalisé :', 'lejournaldesactus'); ?></label>
        <select id="custom_author" name="custom_author" style="width: 100%;">
            <option value=""><?php _e('-- Auteur WordPress par défaut --', 'lejournaldesactus'); ?></option>
            <?php foreach ($authors as $author) : ?>
                <option value="<?php echo esc_attr($author->ID); ?>" <?php selected($selected_author, $author->ID); ?>>
                    <?php echo esc_html($author->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p class="description">
        <?php _e('Si aucun auteur personnalisé n\'est sélectionné, l\'auteur WordPress par défaut sera utilisé.', 'lejournaldesactus'); ?>
    </p>
    <?php
}

/**
 * Sauvegarder l'auteur personnalisé sélectionné
 */
function lejournaldesactus_save_post_author($post_id) {
    // Vérifier si nous devons sauvegarder
    if (!isset($_POST['lejournaldesactus_post_author_nonce'])) {
        return;
    }
    
    // Vérifier si le nonce est valide
    if (!wp_verify_nonce($_POST['lejournaldesactus_post_author_nonce'], 'lejournaldesactus_save_post_author')) {
        return;
    }
    
    // Vérifier si c'est une sauvegarde automatique
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Vérifier les permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Sauvegarder l'auteur sélectionné
    if (isset($_POST['custom_author'])) {
        if (!empty($_POST['custom_author'])) {
            update_post_meta($post_id, '_custom_author', absint($_POST['custom_author']));
        } else {
            delete_post_meta($post_id, '_custom_author');
        }
    }
}
add_action('save_post', 'lejournaldesactus_save_post_author');

/**
 * Fonction pour mettre à jour automatiquement le nombre d'articles d'un auteur
 */
function lejournaldesactus_update_author_articles_count($post_id, $post, $update) {
    // Ne pas exécuter lors d'une sauvegarde automatique
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Vérifier si c'est un article
    if ($post->post_type !== 'post') {
        return;
    }
    
    // Récupérer l'auteur personnalisé
    $author_id = get_post_meta($post_id, '_custom_author', true);
    
    if (!$author_id) {
        return;
    }
    
    // Compter le nombre d'articles de cet auteur
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'meta_key' => '_custom_author',
        'meta_value' => $author_id,
        'post_status' => 'publish',
    );
    
    $posts = get_posts($args);
    $count = count($posts);
    
    // Mettre à jour le compteur d'articles
    update_post_meta($author_id, '_author_articles_count', $count);
}
add_action('save_post', 'lejournaldesactus_update_author_articles_count', 10, 3);
add_action('trash_post', 'lejournaldesactus_update_author_articles_count', 10, 3);
add_action('untrash_post', 'lejournaldesactus_update_author_articles_count', 10, 3);
