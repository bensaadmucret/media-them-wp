<?php
/**
 * Gestion de l'envoi des newsletters
 * 
 * @package LeJournalDesActus
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

class LeJournalDesActus_Newsletter_Sender {
    
    /**
     * Initialisation de la classe
     */
    public static function init() {
        // Ajouter la planification hebdomadaire
        if (!wp_next_scheduled('lejournaldesactus_weekly_newsletter')) {
            wp_schedule_event(time(), 'weekly', 'lejournaldesactus_weekly_newsletter');
        }
        
        // Ajouter la planification pour les notifications immédiates
        if (!wp_next_scheduled('lejournaldesactus_immediate_notification')) {
            wp_schedule_event(time(), 'hourly', 'lejournaldesactus_immediate_notification');
        }
        
        // Hook pour l'envoi hebdomadaire
        add_action('lejournaldesactus_weekly_newsletter', array(__CLASS__, 'send_weekly_digest'));
        
        // Hook pour les notifications immédiates
        add_action('lejournaldesactus_immediate_notification', array(__CLASS__, 'send_immediate_notifications'));
        
        // Hook pour la publication d'un nouvel article
        add_action('publish_post', array(__CLASS__, 'mark_post_for_notification'), 10, 2);
        
        // Ajouter une page dans l'administration
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        
        // Ajouter des champs pour les préférences utilisateur
        add_action('lejournaldesactus_newsletter_form_fields', array(__CLASS__, 'add_preference_fields'));
        
        // Traiter les préférences lors de l'inscription
        add_action('lejournaldesactus_after_subscription_processed', array(__CLASS__, 'save_subscriber_preferences'), 10, 2);
    }
    
    /**
     * Ajouter des options au menu d'administration
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'lejournaldesactus-newsletter',
            __('Envoi de Newsletter', 'lejournaldesactus'),
            __('Envoi de Newsletter', 'lejournaldesactus'),
            'manage_options',
            'lejournaldesactus-newsletter-sender',
            array(__CLASS__, 'admin_page')
        );
    }
    
    /**
     * Page d'administration pour l'envoi de newsletters
     */
    public static function admin_page() {
        // Traitement de l'envoi manuel
        if (isset($_POST['send_newsletter']) && isset($_POST['newsletter_type'])) {
            $newsletter_type = sanitize_text_field($_POST['newsletter_type']);
            
            if ($newsletter_type === 'weekly') {
                $result = self::send_weekly_digest(true);
                if ($result) {
                    echo '<div class="notice notice-success"><p>' . __('Newsletter hebdomadaire envoyée avec succès.', 'lejournaldesactus') . '</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>' . __('Erreur lors de l\'envoi de la newsletter hebdomadaire.', 'lejournaldesactus') . '</p></div>';
                }
            } elseif ($newsletter_type === 'immediate') {
                $result = self::send_immediate_notifications(true);
                if ($result) {
                    echo '<div class="notice notice-success"><p>' . __('Notifications immédiates envoyées avec succès.', 'lejournaldesactus') . '</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>' . __('Erreur lors de l\'envoi des notifications immédiates.', 'lejournaldesactus') . '</p></div>';
                }
            }
        }
        
        // Récupérer les statistiques
        global $wpdb;
        $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
        
        $total_subscribers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'confirmed'");
        $immediate_subscribers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'confirmed' AND immediate_notifications = 1");
        
        // Récupérer la date du dernier envoi
        $last_weekly = get_option('lejournaldesactus_last_weekly_newsletter', __('Jamais', 'lejournaldesactus'));
        $last_immediate = get_option('lejournaldesactus_last_immediate_notification', __('Jamais', 'lejournaldesactus'));
        
        if (is_numeric($last_weekly)) {
            $last_weekly = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_weekly);
        }
        
        if (is_numeric($last_immediate)) {
            $last_immediate = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_immediate);
        }
        
        // Affichage de la page
        ?>
        <div class="wrap">
            <h1><?php _e('Envoi de Newsletter', 'lejournaldesactus'); ?></h1>
            
            <div class="card">
                <h2><?php _e('Statistiques', 'lejournaldesactus'); ?></h2>
                <p><?php printf(__('Nombre total d\'abonnés : %d', 'lejournaldesactus'), $total_subscribers); ?></p>
                <p><?php printf(__('Abonnés aux notifications immédiates : %d', 'lejournaldesactus'), $immediate_subscribers); ?></p>
                <p><?php printf(__('Dernier envoi hebdomadaire : %s', 'lejournaldesactus'), $last_weekly); ?></p>
                <p><?php printf(__('Dernière notification immédiate : %s', 'lejournaldesactus'), $last_immediate); ?></p>
            </div>
            
            <div class="card">
                <h2><?php _e('Envoi manuel', 'lejournaldesactus'); ?></h2>
                <form method="post">
                    <p>
                        <label>
                            <input type="radio" name="newsletter_type" value="weekly" checked>
                            <?php _e('Envoyer le digest hebdomadaire', 'lejournaldesactus'); ?>
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="radio" name="newsletter_type" value="immediate">
                            <?php _e('Envoyer les notifications immédiates en attente', 'lejournaldesactus'); ?>
                        </label>
                    </p>
                    <p>
                        <input type="submit" name="send_newsletter" class="button button-primary" value="<?php esc_attr_e('Envoyer', 'lejournaldesactus'); ?>">
                    </p>
                </form>
            </div>
            
            <div class="card">
                <h2><?php _e('Prochain envoi automatique', 'lejournaldesactus'); ?></h2>
                <p>
                    <?php 
                    $next_weekly = wp_next_scheduled('lejournaldesactus_weekly_newsletter');
                    printf(
                        __('Prochain digest hebdomadaire : %s', 'lejournaldesactus'),
                        date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_weekly)
                    ); 
                    ?>
                </p>
                <p>
                    <?php 
                    $next_immediate = wp_next_scheduled('lejournaldesactus_immediate_notification');
                    printf(
                        __('Prochaine vérification des notifications immédiates : %s', 'lejournaldesactus'),
                        date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_immediate)
                    ); 
                    ?>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Marquer un article pour notification immédiate
     */
    public static function mark_post_for_notification($post_id, $post) {
        // Vérifier si c'est un nouvel article (pas une mise à jour)
        if ($post->post_date === $post->post_modified) {
            // Stocker l'ID de l'article dans une option pour traitement ultérieur
            $pending_posts = get_option('lejournaldesactus_pending_notifications', array());
            if (!in_array($post_id, $pending_posts)) {
                $pending_posts[] = $post_id;
                update_option('lejournaldesactus_pending_notifications', $pending_posts);
            }
        }
    }
    
    /**
     * Envoyer le digest hebdomadaire
     */
    public static function send_weekly_digest($manual = false) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
        
        // Récupérer tous les abonnés confirmés
        $subscribers = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'confirmed'");
        
        if (empty($subscribers)) {
            return false;
        }
        
        // Récupérer les articles de la semaine
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'date_query' => array(
                array(
                    'after' => '1 week ago',
                ),
            ),
        );
        
        $recent_posts = get_posts($args);
        
        if (empty($recent_posts)) {
            // Pas d'articles récents à envoyer
            if ($manual) {
                return false;
            } else {
                return true; // Ne pas considérer comme une erreur pour l'envoi automatique
            }
        }
        
        // Construire le contenu de l'email
        $subject = sprintf(__('[%s] Votre résumé hebdomadaire', 'lejournaldesactus'), get_bloginfo('name'));
        
        // Compteur d'envois réussis
        $success_count = 0;
        
        foreach ($subscribers as $subscriber) {
            $message = self::generate_weekly_email_content($subscriber, $recent_posts);
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            $sent = self::send_email($subscriber->email, $subject, $message);
            
            if ($sent) {
                $success_count++;
            }
        }
        
        // Mettre à jour la date du dernier envoi
        update_option('lejournaldesactus_last_weekly_newsletter', time());
        
        return $success_count > 0;
    }
    
    /**
     * Envoyer les notifications immédiates
     */
    public static function send_immediate_notifications($manual = false) {
        $pending_posts = get_option('lejournaldesactus_pending_notifications', array());
        
        if (empty($pending_posts)) {
            return false;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
        
        // Récupérer les abonnés qui souhaitent des notifications immédiates
        $subscribers = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'confirmed' AND immediate_notifications = 1");
        
        if (empty($subscribers)) {
            // Vider la liste des articles en attente car personne ne souhaite de notifications
            update_option('lejournaldesactus_pending_notifications', array());
            return false;
        }
        
        $success_count = 0;
        
        foreach ($pending_posts as $post_id) {
            $post = get_post($post_id);
            
            if (!$post || $post->post_status !== 'publish') {
                continue;
            }
            
            // Vérifier si l'article est dans une catégorie spécifique
            $categories = get_the_category($post_id);
            $category_ids = array();
            foreach ($categories as $category) {
                $category_ids[] = $category->term_id;
            }
            
            foreach ($subscribers as $subscriber) {
                // Vérifier les préférences de catégories si elles existent
                $preferred_categories = get_user_meta($subscriber->id, 'preferred_categories', true);
                
                // Si l'utilisateur a des préférences et que l'article n'est pas dans ses préférences, passer
                if (!empty($preferred_categories) && !array_intersect($category_ids, $preferred_categories)) {
                    continue;
                }
                
                $subject = sprintf(__('[%s] Nouvel article : %s', 'lejournaldesactus'), get_bloginfo('name'), $post->post_title);
                $message = self::generate_immediate_email_content($subscriber, $post);
                $headers = array('Content-Type: text/html; charset=UTF-8');
                
                $sent = self::send_email($subscriber->email, $subject, $message);
                
                if ($sent) {
                    $success_count++;
                }
            }
        }
        
        // Vider la liste des articles en attente
        update_option('lejournaldesactus_pending_notifications', array());
        
        // Mettre à jour la date du dernier envoi
        update_option('lejournaldesactus_last_immediate_notification', time());
        
        return $success_count > 0;
    }
    
    /**
     * Envoyer un email
     */
    private static function send_email($to, $subject, $message) {
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_option('lejournaldesactus_newsletter_sender_name', get_bloginfo('name')) . ' <' . get_option('lejournaldesactus_newsletter_sender_email', get_option('admin_email')) . '>',
        );
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Générer le contenu HTML pour l'email hebdomadaire
     */
    public static function generate_weekly_email_content($subscriber, $posts) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo get_bloginfo('name'); ?> - Newsletter</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .logo {
                    max-width: 200px;
                    height: auto;
                }
                h1 {
                    color: #2c3e50;
                    margin-bottom: 20px;
                }
                .post {
                    margin-bottom: 30px;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 20px;
                }
                .post:last-child {
                    border-bottom: none;
                }
                .post-title {
                    color: #2980b9;
                    font-size: 20px;
                    margin-bottom: 10px;
                }
                .post-excerpt {
                    margin-bottom: 15px;
                }
                .post-link {
                    display: inline-block;
                    background-color: #3498db;
                    color: white;
                    padding: 8px 15px;
                    text-decoration: none;
                    border-radius: 4px;
                }
                .post-link:hover {
                    background-color: #2980b9;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 12px;
                    color: #7f8c8d;
                }
                .unsubscribe {
                    color: #7f8c8d;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="<?php echo get_bloginfo('name'); ?>" class="logo">
                <h1><?php _e('Votre résumé hebdomadaire', 'lejournaldesactus'); ?></h1>
                <p><?php printf(__('Bonjour %s,', 'lejournaldesactus'), esc_html($subscriber->name)); ?></p>
                <p><?php _e('Voici les derniers articles publiés cette semaine sur notre site :', 'lejournaldesactus'); ?></p>
            </div>
            
            <?php foreach ($posts as $post) : ?>
                <div class="post">
                    <h2 class="post-title"><?php echo esc_html($post->post_title); ?></h2>
                    <div class="post-excerpt">
                        <?php 
                        if (has_post_thumbnail($post->ID)) {
                            echo '<p><img src="' . get_the_post_thumbnail_url($post->ID, 'medium') . '" style="max-width:100%;height:auto;"></p>';
                        }
                        
                        echo '<p>' . wp_trim_words(get_the_excerpt($post), 30, '...') . '</p>';
                        ?>
                    </div>
                    <a href="<?php echo get_permalink($post); ?>" class="post-link"><?php _e('Lire l\'article', 'lejournaldesactus'); ?></a>
                </div>
            <?php endforeach; ?>
            
            <div class="footer">
                <p>
                    <?php printf(
                        __('Vous recevez cet email car vous êtes abonné à la newsletter de %s.', 'lejournaldesactus'),
                        get_bloginfo('name')
                    ); ?>
                </p>
                <p>
                    <?php 
                    $site_url = site_url();
                    $unsubscribe_url = add_query_arg(
                        array(
                            'newsletter_action' => 'unsubscribe',
                            'email' => urlencode($subscriber->email),
                            'key' => $subscriber->confirmation_key
                        ),
                        $site_url . '/wp-content/themes/lejournaldesactus/unsubscribe-newsletter.php'
                    );
                    ?>
                    <a href="<?php echo esc_url($unsubscribe_url); ?>" class="unsubscribe"><?php _e('Se désabonner', 'lejournaldesactus'); ?></a>
                </p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Générer le contenu HTML pour les notifications immédiates
     */
    public static function generate_immediate_email_content($subscriber, $post) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo get_bloginfo('name'); ?> - <?php echo esc_html($post->post_title); ?></title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .logo {
                    max-width: 200px;
                    height: auto;
                }
                h1 {
                    color: #2c3e50;
                    margin-bottom: 20px;
                }
                .post {
                    margin-bottom: 30px;
                }
                .post-title {
                    color: #2980b9;
                    font-size: 24px;
                    margin-bottom: 15px;
                }
                .post-excerpt {
                    margin-bottom: 20px;
                }
                .post-link {
                    display: inline-block;
                    background-color: #3498db;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 4px;
                    font-weight: bold;
                }
                .post-link:hover {
                    background-color: #2980b9;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 12px;
                    color: #7f8c8d;
                }
                .unsubscribe {
                    color: #7f8c8d;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="<?php echo get_bloginfo('name'); ?>" class="logo">
                <h1><?php _e('Nouvel article publié !', 'lejournaldesactus'); ?></h1>
                <p><?php printf(__('Bonjour %s,', 'lejournaldesactus'), esc_html($subscriber->name)); ?></p>
                <p><?php _e('Un nouvel article vient d\'être publié sur notre site :', 'lejournaldesactus'); ?></p>
            </div>
            
            <div class="post">
                <h2 class="post-title"><?php echo esc_html($post->post_title); ?></h2>
                <div class="post-excerpt">
                    <?php 
                    if (has_post_thumbnail($post->ID)) {
                        echo '<p><img src="' . get_the_post_thumbnail_url($post->ID, 'medium') . '" style="max-width:100%;height:auto;"></p>';
                    }
                    
                    echo '<p>' . wp_trim_words(get_the_excerpt($post), 50, '...') . '</p>';
                    ?>
                </div>
                <a href="<?php echo get_permalink($post); ?>" class="post-link"><?php _e('Lire l\'article complet', 'lejournaldesactus'); ?></a>
            </div>
            
            <div class="footer">
                <p>
                    <?php printf(
                        __('Vous recevez cet email car vous êtes abonné aux notifications immédiates de %s.', 'lejournaldesactus'),
                        get_bloginfo('name')
                    ); ?>
                </p>
                <p>
                    <?php 
                    $site_url = site_url();
                    $unsubscribe_url = add_query_arg(
                        array(
                            'newsletter_action' => 'unsubscribe',
                            'email' => urlencode($subscriber->email),
                            'key' => $subscriber->confirmation_key
                        ),
                        $site_url . '/wp-content/themes/lejournaldesactus/unsubscribe-newsletter.php'
                    );
                    ?>
                    <a href="<?php echo esc_url($unsubscribe_url); ?>" class="unsubscribe"><?php _e('Se désabonner', 'lejournaldesactus'); ?></a>
                </p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Ajouter des champs de préférences au formulaire d'inscription
     */
    public static function add_preference_fields() {
        ?>
        <div class="newsletter-preferences">
            <h4><?php _e('Préférences de notification', 'lejournaldesactus'); ?></h4>
            <p>
                <label>
                    <input type="checkbox" name="immediate_notifications" value="1">
                    <?php _e('Je souhaite recevoir des notifications immédiates pour les nouveaux articles', 'lejournaldesactus'); ?>
                </label>
            </p>
            
            <h4><?php _e('Catégories préférées', 'lejournaldesactus'); ?></h4>
            <?php
            $categories = get_categories(array(
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
            ));
            
            if (!empty($categories)) {
                echo '<div class="newsletter-categories">';
                foreach ($categories as $category) {
                    ?>
                    <label>
                        <input type="checkbox" name="preferred_categories[]" value="<?php echo $category->term_id; ?>">
                        <?php echo esc_html($category->name); ?>
                    </label><br>
                    <?php
                }
                echo '</div>';
            }
            ?>
        </div>
        <?php
    }
    
    /**
     * Sauvegarder les préférences de l'abonné
     */
    public static function save_subscriber_preferences($subscriber_id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
        
        // Notifications immédiates
        $immediate_notifications = isset($data['immediate_notifications']) ? 1 : 0;
        
        $wpdb->update(
            $table_name,
            array('immediate_notifications' => $immediate_notifications),
            array('id' => $subscriber_id)
        );
        
        // Catégories préférées
        if (isset($data['preferred_categories']) && is_array($data['preferred_categories'])) {
            $preferred_categories = array_map('intval', $data['preferred_categories']);
            update_user_meta($subscriber_id, 'preferred_categories', $preferred_categories);
        }
    }
}

// Initialiser la classe
add_action('init', array('LeJournalDesActus_Newsletter_Sender', 'init'));
