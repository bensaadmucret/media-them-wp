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
        'rewrite'            => array(
            'slug' => 'redacteur',
            'with_front' => false,
            'feeds' => true,
            'pages' => true
        ),
        'capability_type'    => 'post',
        'has_archive'        => 'redacteurs',
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-users',
        'supports'           => array('title', 'editor', 'thumbnail'),
    );

    register_post_type('custom_author', $args);
    
    // Enregistrer l'ancien type de contenu pour la compatibilité
    register_post_type('author', array(
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => false,
        'show_in_menu' => false,
        'query_var' => true,
        'rewrite' => array('slug' => 'auteur'),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'thumbnail'),
    ));
}
add_action('init', 'lejournaldesactus_register_author_post_type');

/**
 * Rediriger les URL de custom_author vers author
 */
function lejournaldesactus_redirect_custom_author() {
    global $post, $wp_query;
    
    // Vérifier si nous sommes sur une page d'auteur personnalisé
    if (is_singular('custom_author')) {
        // Forcer WordPress à utiliser le bon template
        // Nous ne redirigeons pas, mais nous nous assurons que le bon template est utilisé
        add_filter('template_include', function($template) {
            $new_template = locate_template(array('single-custom_author.php'));
            if (!empty($new_template)) {
                return $new_template;
            }
            return $template;
        }, 99);
    }
}
add_action('template_redirect', 'lejournaldesactus_redirect_custom_author', 5);

/**
 * Utiliser le template single-author.php pour les deux types de contenu
 */
function lejournaldesactus_use_author_template($template) {
    if (is_singular('custom_author')) {
        $new_template = locate_template(array('single-custom_author.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'lejournaldesactus_use_author_template');

/**
 * Fonction pour forcer la mise à jour des permaliens
 * Cette fonction sera exécutée une seule fois
 */
function lejournaldesactus_flush_rewrite_rules() {
    // Vérifier si les règles ont déjà été mises à jour
    if (get_option('lejournaldesactus_author_rewrite_flushed') !== '1') {
        // Mettre à jour les règles de réécriture
        flush_rewrite_rules();
        
        // Marquer comme fait pour ne pas le refaire
        update_option('lejournaldesactus_author_rewrite_flushed', '1');
        
        // Journaliser l'action
        error_log('Règles de réécriture mises à jour pour les types de contenu author et custom_author');
    }
}
add_action('init', 'lejournaldesactus_flush_rewrite_rules', 20);

/**
 * Fonction pour migrer automatiquement tous les custom_author vers author
 */
function lejournaldesactus_migrate_all_authors() {
    // Vérifier si la migration a déjà été effectuée
    if (get_option('lejournaldesactus_authors_migrated')) {
        return;
    }
    
    // Vérifier si nous sommes dans un contexte d'administration
    if (!is_admin()) {
        return;
    }
    
    // Récupérer tous les auteurs personnalisés
    $custom_authors = get_posts(array(
        'post_type'      => 'custom_author',
        'post_status'    => 'publish',
        'posts_per_page' => 50, // Limiter à 50 auteurs à la fois pour éviter les timeouts
        'meta_query'     => array(
            array(
                'key'     => '_migrated_to_author',
                'compare' => 'NOT EXISTS'
            )
        )
    ));
    
    if (empty($custom_authors)) {
        // Marquer la migration comme terminée
        update_option('lejournaldesactus_authors_migrated', true);
        return;
    }
    
    $count = 0;
    
    foreach ($custom_authors as $author) {
        // Vérifier si l'auteur a déjà été migré
        $migrated = get_post_meta($author->ID, '_migrated_to_author', true);
        if (!empty($migrated)) {
            continue;
        }
        
        // Créer un nouvel auteur
        $new_author_id = wp_insert_post(array(
            'post_title'   => $author->post_title,
            'post_content' => $author->post_content,
            'post_status'  => 'publish',
            'post_type'    => 'author',
            'post_name'    => sanitize_title($author->post_title),
        ));
        
        if (is_wp_error($new_author_id)) {
            error_log('Erreur lors de la migration de l\'auteur ' . $author->ID . ': ' . $new_author_id->get_error_message());
            continue;
        }
        
        // Copier les métadonnées
        $meta_keys = array(
            '_author_role',
            '_author_email',
            '_author_website',
            '_author_twitter',
            '_author_facebook',
            '_author_linkedin',
            '_author_instagram',
            '_author_articles_count',
            '_author_bio'
        );
        
        foreach ($meta_keys as $key) {
            $value = get_post_meta($author->ID, $key, true);
            if (!empty($value)) {
                update_post_meta($new_author_id, $key, $value);
            }
        }
        
        // Copier l'image mise en avant
        $thumbnail_id = get_post_thumbnail_id($author->ID);
        if ($thumbnail_id) {
            set_post_thumbnail($new_author_id, $thumbnail_id);
        }
        
        // Marquer l'ancien auteur comme migré
        update_post_meta($author->ID, '_migrated_to_author', $new_author_id);
        
        // Mettre à jour les articles associés à cet auteur
        $posts = get_posts(array(
            'post_type'      => 'post',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'meta_key'       => '_custom_author',
            'meta_value'     => $author->ID
        ));
        
        foreach ($posts as $post) {
            update_post_meta($post->ID, '_custom_author', $new_author_id);
        }
        
        $count++;
        
        // Limiter le nombre d'auteurs migrés par lot pour éviter les timeouts
        if ($count >= 10) {
            break;
        }
    }
    
    // Si tous les auteurs ont été traités, marquer la migration comme terminée
    if (count($custom_authors) < 50 || $count < 10) {
        update_option('lejournaldesactus_authors_migrated', true);
        error_log('Migration des auteurs terminée. ' . $count . ' auteurs migrés.');
    }
}
add_action('admin_init', 'lejournaldesactus_migrate_all_authors');

/**
 * Ajouter les métadonnées personnalisées pour les auteurs
 */
function lejournaldesactus_add_author_meta_boxes() {
    add_meta_box(
        'author_details',
        __('Détails de l\'auteur', 'lejournaldesactus'),
        'lejournaldesactus_author_details_callback',
        'custom_author',
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
    <div class="author-details-metabox">
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
add_action('save_post_custom_author', 'lejournaldesactus_save_author_data');
// Supprimer le hook pour les posts de type 'author'
// add_action('save_post_author', 'lejournaldesactus_save_author_data');

/**
 * Ajouter un champ pour sélectionner un auteur personnalisé dans les articles
 * Cette fonction est désactivée car nous utilisons la métabox d'auteur standard de WordPress
 */
function lejournaldesactus_add_post_author_meta_box() {
    // Fonction désactivée
    return;
    
    // Code original conservé en commentaire pour référence
    /*
    add_meta_box(
        'post_custom_author',
        __('Auteur personnalisé', 'lejournaldesactus'),
        'lejournaldesactus_post_author_callback',
        'post',
        'normal',
        'high'
    );
    */
}

// Désactiver l'ajout de la métabox d'auteur personnalisé
// add_action('add_meta_boxes', 'lejournaldesactus_add_post_author_meta_box');

/**
 * Callback pour afficher le sélecteur d'auteur personnalisé
 */
function lejournaldesactus_post_author_callback($post) {
    // Ajouter un nonce pour la vérification
    wp_nonce_field('lejournaldesactus_save_post_author', 'lejournaldesactus_post_author_nonce');
    
    // Récupérer l'auteur sélectionné
    $selected_author = get_post_meta($post->ID, '_custom_author', true);
    
    // Récupérer tous les auteurs (utiliser 'author' au lieu de 'custom_author' selon la mémoire)
    $authors = get_posts(array(
        'post_type' => 'author',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish',
    ));
    
    // Afficher le sélecteur
    ?>
    <p>
        <label for="custom_author"><?php _e('Auteur personnalisé:', 'lejournaldesactus'); ?></label>
        <br />
        <select id="custom_author" name="custom_author" class="full-width">
            <option value=""><?php _e('-- Sélectionner un auteur --', 'lejournaldesactus'); ?></option>
            <?php foreach ($authors as $author) : ?>
                <option value="<?php echo $author->ID; ?>" <?php selected($selected_author, $author->ID); ?>><?php echo $author->post_title; ?></option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Sélectionnez un auteur personnalisé pour cet article. Cela remplacera l\'auteur WordPress standard dans l\'affichage.', 'lejournaldesactus'); ?></p>
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
    
    // Vérifier si c'est une révision
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Vérifier le type de post (uniquement pour les articles)
    if (get_post_type($post_id) !== 'post') {
        return;
    }
    
    // Sauvegarder l'auteur sélectionné
    if (isset($_POST['custom_author'])) {
        if (!empty($_POST['custom_author'])) {
            update_post_meta($post_id, '_custom_author', absint($_POST['custom_author']));
            
            // Mettre à jour le compteur d'articles pour cet auteur
            $author_id = absint($_POST['custom_author']);
            $count = intval(get_post_meta($author_id, '_author_articles_count', true));
            update_post_meta($author_id, '_author_articles_count', $count + 1);
            
            // Log pour le débogage
            error_log('Auteur personnalisé associé à l\'article ' . $post_id . ': ' . $author_id);
        } else {
            // Récupérer l'ancien auteur pour mettre à jour son compteur
            $old_author_id = get_post_meta($post_id, '_custom_author', true);
            if (!empty($old_author_id)) {
                $count = intval(get_post_meta($old_author_id, '_author_articles_count', true));
                if ($count > 0) {
                    update_post_meta($old_author_id, '_author_articles_count', $count - 1);
                }
            }
            
            delete_post_meta($post_id, '_custom_author');
            error_log('Auteur personnalisé supprimé de l\'article ' . $post_id);
        }
    }
}
remove_action('save_post', 'lejournaldesactus_save_post_author');
add_action('save_post_post', 'lejournaldesactus_save_post_author', 10, 1);

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

/**
 * Fonction pour rediriger les anciennes URLs d'auteurs vers les nouvelles
 */
function lejournaldesactus_redirect_author_urls() {
    // Vérifier si nous sommes sur une page 404
    if (is_404()) {
        // Récupérer l'URL actuelle de manière sécurisée
        $current_url = esc_url_raw(add_query_arg(array()));
        $path = wp_parse_url($current_url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        
        // Vérifier si l'URL contient /author/ ou /redacteur/ ou /auteur/
        if (strpos($path, 'author/') !== false || strpos($path, 'redacteur/') !== false || strpos($path, 'auteur/') !== false) {
            // Extraire le slug de l'auteur de manière sécurisée
            $path_parts = explode('/', $path);
            $author_slug = end($path_parts);
            $author_slug = sanitize_title($author_slug);
            
            if (!empty($author_slug)) {
                // Rechercher l'auteur par son slug
                $args = array(
                    'name'        => $author_slug,
                    'post_type'   => 'custom_author',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );
                $author_posts = get_posts($args);
                
                if ($author_posts) {
                    // Rediriger vers la nouvelle URL
                    $new_url = get_permalink($author_posts[0]->ID);
                    wp_redirect($new_url, 301);
                    exit;
                }
            }
        }
    }
}
add_action('template_redirect', 'lejournaldesactus_redirect_author_urls');

/**
 * Fonction pour supprimer l'auteur personnalisé lorsque l'auteur WordPress standard est modifié
 */
function lejournaldesactus_handle_post_author_change($post_ID, $post_after, $post_before) {
    // Vérifier si l'auteur a changé
    if ($post_after->post_author !== $post_before->post_author) {
        // Si l'auteur WordPress a changé, supprimer l'association avec l'auteur personnalisé
        $old_author_id = get_post_meta($post_ID, '_custom_author', true);
        if (!empty($old_author_id)) {
            // Mettre à jour le compteur d'articles pour l'ancien auteur
            $count = intval(get_post_meta($old_author_id, '_author_articles_count', true));
            if ($count > 0) {
                update_post_meta($old_author_id, '_author_articles_count', $count - 1);
            }
            
            // Supprimer l'association
            delete_post_meta($post_ID, '_custom_author');
            
            // Log pour le débogage
            error_log('Auteur WordPress modifié pour l\'article ' . $post_ID . '. Suppression de l\'auteur personnalisé.');
        }
    }
}
add_action('post_updated', 'lejournaldesactus_handle_post_author_change', 10, 3);

/**
 * Fonction pour synchroniser l'auteur personnalisé avec l'auteur WordPress
 * Cette fonction est appelée à chaque fois qu'un article est mis à jour
 */
function lejournaldesactus_sync_post_author($post_id) {
    // Ne pas exécuter lors d'une sauvegarde automatique ou d'une révision
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id)) {
        return;
    }
    
    // Vérifier si c'est un article
    if (get_post_type($post_id) !== 'post') {
        return;
    }
    
    // Vérifier si nous sommes dans un contexte d'administration
    if (!is_admin()) {
        return;
    }
    
    // Forcer la mise à jour du compteur d'articles pour tous les auteurs
    lejournaldesactus_update_all_authors_count();
    
    // Log pour le débogage
    error_log('Synchronisation des auteurs pour l\'article ' . $post_id);
}

/**
 * Fonction pour mettre à jour le compteur d'articles pour tous les auteurs
 */
function lejournaldesactus_update_all_authors_count() {
    // Récupérer tous les auteurs, limité à 20 à la fois
    $authors = get_posts(array(
        'post_type' => 'author',
        'posts_per_page' => 20,
        'post_status' => 'publish',
        'orderby' => 'ID',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_last_count_update',
                'compare' => 'NOT EXISTS',
                // Ou utiliser cette ligne pour mettre à jour périodiquement
                // 'value' => time() - 86400, // 24 heures
                // 'compare' => '<',
                // 'type' => 'NUMERIC'
            )
        )
    ));
    
    if (empty($authors)) {
        // Réinitialiser le marqueur pour permettre une mise à jour future
        delete_option('lejournaldesactus_authors_count_updated');
        return;
    }
    
    foreach ($authors as $author) {
        // Utiliser une requête SQL directe pour compter les articles plus efficacement
        global $wpdb;
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->postmeta 
            JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->postmeta.post_id
            WHERE meta_key = '_custom_author' 
            AND meta_value = %d
            AND $wpdb->posts.post_status = 'publish'
            AND $wpdb->posts.post_type = 'post'",
            $author->ID
        ));
        
        // Mettre à jour le compteur d'articles
        update_post_meta($author->ID, '_author_articles_count', intval($count));
        
        // Marquer cet auteur comme mis à jour
        update_post_meta($author->ID, '_last_count_update', time());
    }
    
    // Marquer la mise à jour comme en cours
    update_option('lejournaldesactus_authors_count_updated', time());
}

// Ajouter les hooks pour la synchronisation des auteurs
add_action('save_post_post', 'lejournaldesactus_sync_post_author', 20, 1);
add_action('before_delete_post', 'lejournaldesactus_sync_post_author', 10, 1);
add_action('wp_trash_post', 'lejournaldesactus_sync_post_author', 10, 1);
add_action('untrash_post', 'lejournaldesactus_sync_post_author', 10, 1);
