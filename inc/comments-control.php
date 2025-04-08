<?php
/**
 * Fonctionnalités pour le contrôle des commentaires
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Ajouter les options de contrôle des commentaires au Customizer
 */
function lejournaldesactus_comments_control_customizer($wp_customize) {
    // Ajouter une section pour le contrôle des commentaires
    $wp_customize->add_section('lejournaldesactus_comments_section', array(
        'title'       => __('Contrôle des commentaires', 'lejournaldesactus'),
        'description' => __('Options pour activer ou désactiver les commentaires sur différents types de contenu.', 'lejournaldesactus'),
        'priority'    => 160,
    ));

    // Option pour désactiver tous les commentaires
    $wp_customize->add_setting('lejournaldesactus_disable_all_comments', array(
        'default'           => false,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lejournaldesactus_disable_all_comments', array(
        'label'       => __('Désactiver tous les commentaires', 'lejournaldesactus'),
        'description' => __('Désactive complètement les commentaires sur tout le site.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_comments_section',
        'type'        => 'checkbox',
    ));

    // Option pour désactiver les commentaires sur les articles
    $wp_customize->add_setting('lejournaldesactus_disable_post_comments', array(
        'default'           => false,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lejournaldesactus_disable_post_comments', array(
        'label'       => __('Désactiver les commentaires sur les articles', 'lejournaldesactus'),
        'description' => __('Désactive les commentaires uniquement sur les articles.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_comments_section',
        'type'        => 'checkbox',
        'active_callback' => function() {
            return !get_theme_mod('lejournaldesactus_disable_all_comments', false);
        },
    ));

    // Option pour désactiver les commentaires sur les pages
    $wp_customize->add_setting('lejournaldesactus_disable_page_comments', array(
        'default'           => false,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lejournaldesactus_disable_page_comments', array(
        'label'       => __('Désactiver les commentaires sur les pages', 'lejournaldesactus'),
        'description' => __('Désactive les commentaires uniquement sur les pages.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_comments_section',
        'type'        => 'checkbox',
        'active_callback' => function() {
            return !get_theme_mod('lejournaldesactus_disable_all_comments', false);
        },
    ));

    // Option pour fermer automatiquement les commentaires après X jours
    $wp_customize->add_setting('lejournaldesactus_auto_close_comments', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lejournaldesactus_auto_close_comments', array(
        'label'       => __('Fermer automatiquement les commentaires', 'lejournaldesactus'),
        'description' => __('Nombre de jours après publication. 0 = ne pas fermer automatiquement.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_comments_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 365,
            'step' => 1,
        ),
        'active_callback' => function() {
            return !get_theme_mod('lejournaldesactus_disable_all_comments', false);
        },
    ));

    // Option pour activer la modération avancée
    $wp_customize->add_setting('lejournaldesactus_advanced_moderation', array(
        'default'           => false,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lejournaldesactus_advanced_moderation', array(
        'label'       => __('Activer la modération avancée', 'lejournaldesactus'),
        'description' => __('Modération plus stricte des commentaires (filtre anti-spam, mots interdits, etc.).', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_comments_section',
        'type'        => 'checkbox',
        'active_callback' => function() {
            return !get_theme_mod('lejournaldesactus_disable_all_comments', false);
        },
    ));

    // Option pour les mots interdits
    $wp_customize->add_setting('lejournaldesactus_blacklist_words', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('lejournaldesactus_blacklist_words', array(
        'label'       => __('Mots interdits', 'lejournaldesactus'),
        'description' => __('Liste de mots interdits dans les commentaires, séparés par des virgules.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_comments_section',
        'type'        => 'textarea',
        'active_callback' => function() {
            return !get_theme_mod('lejournaldesactus_disable_all_comments', false) && 
                   get_theme_mod('lejournaldesactus_advanced_moderation', false);
        },
    ));
}
add_action('customize_register', 'lejournaldesactus_comments_control_customizer');

/**
 * Sanitize checkbox
 */
if (!function_exists('lejournaldesactus_sanitize_checkbox')) {
    function lejournaldesactus_sanitize_checkbox($input) {
        return (isset($input) && true == $input) ? true : false;
    }
}

/**
 * Désactiver les commentaires selon les paramètres
 */
function lejournaldesactus_disable_comments() {
    // Vérifier si tous les commentaires sont désactivés
    $disable_all = get_theme_mod('lejournaldesactus_disable_all_comments', false);
    
    if ($disable_all) {
        // Désactiver la prise en charge des commentaires
        add_filter('comments_open', '__return_false', 20, 2);
        add_filter('pings_open', '__return_false', 20, 2);
        
        // Masquer les commentaires existants
        add_filter('comments_array', '__return_empty_array', 10, 2);
        
        // Supprimer les commentaires du menu admin
        add_action('admin_menu', function() {
            remove_menu_page('edit-comments.php');
        });
        
        // Supprimer les commentaires de la barre d'administration
        add_action('wp_before_admin_bar_render', function() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('comments');
        });
        
        // Désactiver le support des commentaires dans les types de publication
        add_action('init', function() {
            $post_types = get_post_types();
            foreach ($post_types as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        });
        
        // Masquer les widgets de commentaires récents
        add_filter('widget_comments_args', function($args) {
            return array_merge($args, array('number' => 0));
        });
        
        // Masquer les métaboxes de commentaires dans l'admin
        add_action('admin_init', function() {
            $post_types = get_post_types();
            foreach ($post_types as $post_type) {
                remove_meta_box('commentsdiv', $post_type, 'normal');
                remove_meta_box('commentstatusdiv', $post_type, 'normal');
            }
        });
    } else {
        // Désactiver les commentaires sur les articles si l'option est activée
        $disable_post_comments = get_theme_mod('lejournaldesactus_disable_post_comments', false);
        if ($disable_post_comments) {
            add_filter('comments_open', function($open, $post_id) {
                $post = get_post($post_id);
                if ($post->post_type == 'post') {
                    return false;
                }
                return $open;
            }, 10, 2);
        }
        
        // Désactiver les commentaires sur les pages si l'option est activée
        $disable_page_comments = get_theme_mod('lejournaldesactus_disable_page_comments', false);
        if ($disable_page_comments) {
            add_filter('comments_open', function($open, $post_id) {
                $post = get_post($post_id);
                if ($post->post_type == 'page') {
                    return false;
                }
                return $open;
            }, 10, 2);
        }
    }
}
add_action('after_setup_theme', 'lejournaldesactus_disable_comments');

/**
 * Fermer automatiquement les commentaires après X jours
 */
function lejournaldesactus_auto_close_comments() {
    $days = get_theme_mod('lejournaldesactus_auto_close_comments', 0);
    
    if ($days > 0) {
        add_filter('comments_open', function($open, $post_id) use ($days) {
            $post = get_post($post_id);
            $post_time = strtotime($post->post_date_gmt);
            $current_time = current_time('timestamp', true);
            
            // Fermer les commentaires si le post est plus vieux que X jours
            if (($current_time - $post_time) > ($days * DAY_IN_SECONDS)) {
                return false;
            }
            
            return $open;
        }, 10, 2);
    }
}
add_action('after_setup_theme', 'lejournaldesactus_auto_close_comments');

/**
 * Ajouter la modération avancée des commentaires
 */
function lejournaldesactus_advanced_moderation() {
    $advanced_moderation = get_theme_mod('lejournaldesactus_advanced_moderation', false);
    
    if ($advanced_moderation) {
        // Ajouter des mots à la liste noire de WordPress
        add_filter('wp_blacklist_check_comment_content', function($content) {
            $blacklist_words = get_theme_mod('lejournaldesactus_blacklist_words', '');
            if (!empty($blacklist_words)) {
                $words = explode(',', $blacklist_words);
                foreach ($words as $word) {
                    $word = trim($word);
                    if (!empty($word) && stripos($content, $word) !== false) {
                        return true; // Commentaire contient un mot interdit
                    }
                }
            }
            return false;
        });
        
        // Augmenter la sévérité du filtre anti-spam
        add_filter('comment_moderation_trigger_words', function($words) {
            $additional_words = array(
                'viagra', 'cialis', 'casino', 'porn', 'xxx',
                'buy now', 'free offer', 'discount', 'cheap',
                'click here', 'enlarge', 'weight loss', 'diet',
                'http://', 'https://', 'www.'
            );
            return array_merge($words, $additional_words);
        });
        
        // Exiger plus de caractères pour les commentaires
        add_filter('preprocess_comment', function($commentdata) {
            if (strlen($commentdata['comment_content']) < 10) {
                wp_die(__('Le commentaire est trop court. Veuillez écrire un commentaire plus détaillé.', 'lejournaldesactus'));
            }
            return $commentdata;
        });
        
        // Limiter le nombre de liens dans les commentaires
        add_filter('comment_max_links_url', function() {
            return 2; // Maximum 2 liens par commentaire
        });
    }
}
add_action('after_setup_theme', 'lejournaldesactus_advanced_moderation');

/**
 * Ajouter un bouton pour désactiver les commentaires dans l'éditeur de post
 */
function lejournaldesactus_add_comment_metabox() {
    // Ne pas ajouter la métabox si tous les commentaires sont désactivés
    if (get_theme_mod('lejournaldesactus_disable_all_comments', false)) {
        return;
    }
    
    // Ne pas ajouter la métabox si les commentaires sont désactivés pour ce type de post
    $disable_post_comments = get_theme_mod('lejournaldesactus_disable_post_comments', false);
    $disable_page_comments = get_theme_mod('lejournaldesactus_disable_page_comments', false);
    
    $post_types = get_post_types(array('public' => true));
    foreach ($post_types as $post_type) {
        if (($post_type == 'post' && $disable_post_comments) || 
            ($post_type == 'page' && $disable_page_comments)) {
            continue;
        }
        
        add_meta_box(
            'lejournaldesactus_comment_control',
            __('Contrôle des commentaires', 'lejournaldesactus'),
            'lejournaldesactus_comment_metabox_callback',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'lejournaldesactus_add_comment_metabox');

/**
 * Callback pour la métabox de contrôle des commentaires
 */
function lejournaldesactus_comment_metabox_callback($post) {
    wp_nonce_field('lejournaldesactus_comment_control_nonce', 'comment_control_nonce');
    
    $disable_comments = get_post_meta($post->ID, '_lejournaldesactus_disable_comments', true);
    ?>
    <p>
        <input type="checkbox" id="lejournaldesactus_disable_comments" name="lejournaldesactus_disable_comments" <?php checked($disable_comments, 'on'); ?> />
        <label for="lejournaldesactus_disable_comments"><?php _e('Désactiver les commentaires sur ce contenu', 'lejournaldesactus'); ?></label>
    </p>
    <p class="description">
        <?php _e('Cette option remplace les paramètres globaux pour ce contenu spécifique.', 'lejournaldesactus'); ?>
    </p>
    <?php
}

/**
 * Sauvegarder les données de la métabox
 */
function lejournaldesactus_save_comment_metabox($post_id) {
    // Vérifier le nonce
    if (!isset($_POST['comment_control_nonce']) || !wp_verify_nonce($_POST['comment_control_nonce'], 'lejournaldesactus_comment_control_nonce')) {
        return;
    }
    
    // Vérifier les autorisations
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Vérifier si c'est une sauvegarde automatique
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Sauvegarder l'option
    if (isset($_POST['lejournaldesactus_disable_comments'])) {
        update_post_meta($post_id, '_lejournaldesactus_disable_comments', 'on');
    } else {
        delete_post_meta($post_id, '_lejournaldesactus_disable_comments');
    }
}
add_action('save_post', 'lejournaldesactus_save_comment_metabox');

/**
 * Appliquer les paramètres de désactivation par post
 */
function lejournaldesactus_apply_per_post_comment_settings($open, $post_id) {
    // Ne pas appliquer si tous les commentaires sont désactivés
    if (get_theme_mod('lejournaldesactus_disable_all_comments', false)) {
        return false;
    }
    
    // Vérifier si les commentaires sont désactivés pour ce post spécifique
    $disable_comments = get_post_meta($post_id, '_lejournaldesactus_disable_comments', true);
    if ($disable_comments == 'on') {
        return false;
    }
    
    return $open;
}
add_filter('comments_open', 'lejournaldesactus_apply_per_post_comment_settings', 10, 2);
