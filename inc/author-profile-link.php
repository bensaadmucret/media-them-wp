<?php
/**
 * Fonctionnalités pour lier les comptes utilisateurs WordPress aux profils d'auteurs personnalisés
 *
 * @package LeJournalDesActus
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Classe pour gérer les liens entre utilisateurs WordPress et profils d'auteurs personnalisés
 */
class LeJournalDesActus_Author_Profile_Link {

    /**
     * Initialisation de la classe
     */
    public function __construct() {
        // Ajouter un champ dans le profil utilisateur
        add_action('show_user_profile', array($this, 'add_author_profile_field'));
        add_action('edit_user_profile', array($this, 'add_author_profile_field'));
        
        // Sauvegarder le champ du profil utilisateur
        add_action('personal_options_update', array($this, 'save_author_profile_field'));
        add_action('edit_user_profile_update', array($this, 'save_author_profile_field'));
        
        // Filtrer l'affichage de l'auteur si nécessaire
        add_filter('get_the_author_display_name', array($this, 'filter_author_display_name'), 10, 2);
        add_filter('author_link', array($this, 'filter_author_link'), 10, 2);
        
        // Ajouter une métabox dans le CPT custom_author pour lier à un utilisateur WP
        add_action('add_meta_boxes', array($this, 'add_user_link_meta_box'));
        add_action('save_post_custom_author', array($this, 'save_user_link_meta_box'));
        
        // Ajouter un lien rapide dans l'administration
        add_action('admin_menu', array($this, 'add_author_link_menu'));
    }

    /**
     * Ajouter un champ pour lier le profil d'auteur personnalisé
     */
    public function add_author_profile_field($user) {
        // Vérifier si l'utilisateur peut publier des articles
        if (!user_can($user, 'publish_posts')) {
            return;
        }
        
        // Récupérer le profil d'auteur lié
        $linked_profile_id = get_user_meta($user->ID, '_linked_author_profile', true);
        
        // Récupérer tous les profils d'auteurs personnalisés
        $author_profiles = get_posts(array(
            'post_type' => 'custom_author',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        // Ajouter un nonce pour la sécurité
        wp_nonce_field('save_author_profile_field_' . $user->ID, 'lejournaldesactus_author_profile_nonce');
        
        ?>
        <h3><?php _e('Profil d\'auteur personnalisé', 'lejournaldesactus'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="linked_author_profile"><?php _e('Profil d\'auteur lié', 'lejournaldesactus'); ?></label></th>
                <td>
                    <select name="linked_author_profile" id="linked_author_profile">
                        <option value=""><?php _e('-- Aucun profil lié --', 'lejournaldesactus'); ?></option>
                        <?php foreach ($author_profiles as $profile) : ?>
                            <option value="<?php echo esc_attr($profile->ID); ?>" <?php selected($linked_profile_id, $profile->ID); ?>>
                                <?php echo esc_html($profile->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('Associer ce compte utilisateur à un profil d\'auteur personnalisé.', 'lejournaldesactus'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Sauvegarder le champ de profil d'auteur
     */
    public function save_author_profile_field($user_id) {
        // Vérifier les permissions
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        
        // Vérifier le nonce
        if (!isset($_POST['lejournaldesactus_author_profile_nonce']) || !wp_verify_nonce($_POST['lejournaldesactus_author_profile_nonce'], 'save_author_profile_field_' . $user_id)) {
            return false;
        }
        
        // Mettre à jour la métadonnée
        if (isset($_POST['linked_author_profile'])) {
            update_user_meta($user_id, '_linked_author_profile', sanitize_text_field($_POST['linked_author_profile']));
        }
    }

    /**
     * Filtrer le nom d'affichage de l'auteur
     */
    public function filter_author_display_name($display_name, $user_id) {
        // Récupérer le profil d'auteur lié
        $linked_profile_id = get_user_meta($user_id, '_linked_author_profile', true);
        
        if ($linked_profile_id) {
            $profile = get_post($linked_profile_id);
            if ($profile && $profile->post_type === 'custom_author') {
                return $profile->post_title;
            }
        }
        
        return $display_name;
    }

    /**
     * Filtrer le lien de l'auteur
     */
    public function filter_author_link($link, $user_id) {
        // Récupérer le profil d'auteur lié
        $linked_profile_id = get_user_meta($user_id, '_linked_author_profile', true);
        
        if ($linked_profile_id) {
            $profile = get_post($linked_profile_id);
            if ($profile && $profile->post_type === 'custom_author') {
                return get_permalink($profile->ID);
            }
        }
        
        return $link;
    }

    /**
     * Ajouter une métabox pour lier un utilisateur WP
     */
    public function add_user_link_meta_box() {
        add_meta_box(
            'user_link_meta_box',
            __('Lier un utilisateur WP', 'lejournaldesactus'),
            array($this, 'display_user_link_meta_box'),
            'custom_author',
            'advanced',
            'high'
        );
    }

    /**
     * Afficher la métabox pour lier un utilisateur WP
     */
    public function display_user_link_meta_box($post) {
        // Récupérer l'utilisateur lié
        $linked_user_id = get_post_meta($post->ID, '_linked_user', true);
        
        // Récupérer tous les utilisateurs WP
        $users = get_users(array(
            'fields' => array('ID', 'display_name')
        ));
        
        ?>
        <select name="linked_user" id="linked_user">
            <option value=""><?php _e('-- Aucun utilisateur lié --', 'lejournaldesactus'); ?></option>
            <?php foreach ($users as $user) : ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($linked_user_id, $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Associer ce profil d\'auteur personnalisé à un utilisateur WP.', 'lejournaldesactus'); ?></p>
        <?php
    }

    /**
     * Sauvegarder la métabox pour lier un utilisateur WP
     */
    public function save_user_link_meta_box($post_id) {
        // Vérifier les permissions
        if (!current_user_can('edit_post', $post_id)) {
            return false;
        }
        
        // Mettre à jour la métadonnée
        if (isset($_POST['linked_user'])) {
            update_post_meta($post_id, '_linked_user', sanitize_text_field($_POST['linked_user']));
        }
    }

    /**
     * Ajouter un lien rapide dans l'administration
     */
    public function add_author_link_menu() {
        add_menu_page(
            __('Liens d\'auteurs', 'lejournaldesactus'),
            __('Liens d\'auteurs', 'lejournaldesactus'),
            'manage_options',
            'author-links',
            array($this, 'display_author_links_page')
        );
    }

    /**
     * Afficher la page des liens d'auteurs
     */
    public function display_author_links_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Liens d\'auteurs', 'lejournaldesactus'); ?></h1>
            <p><?php _e('Cette page affiche les liens entre les profils d\'auteurs personnalisés et les utilisateurs WP.', 'lejournaldesactus'); ?></p>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Profil d\'auteur', 'lejournaldesactus'); ?></th>
                        <th><?php _e('Utilisateur WP', 'lejournaldesactus'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Récupérer tous les profils d'auteurs personnalisés
                    $author_profiles = get_posts(array(
                        'post_type' => 'custom_author',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC'
                    ));
                    
                    foreach ($author_profiles as $profile) :
                        // Récupérer l'utilisateur lié
                        $linked_user_id = get_post_meta($profile->ID, '_linked_user', true);
                        
                        // Récupérer l'utilisateur WP
                        $user = get_user_by('id', $linked_user_id);
                    ?>
                    <tr>
                        <td><?php echo esc_html($profile->post_title); ?></td>
                        <td><?php echo esc_html($user->display_name); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

// Initialiser la classe
$lejournaldesactus_author_profile_link = new LeJournalDesActus_Author_Profile_Link();
