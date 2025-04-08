<?php
/**
 * Fonctions pour l'enregistrement et la gestion des widgets avancés
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Inclure les fichiers des widgets
 */
require_once get_template_directory() . '/inc/widgets/recent-posts-widget.php';
require_once get_template_directory() . '/inc/widgets/popular-posts-widget.php';
require_once get_template_directory() . '/inc/widgets/newsletter-widget.php';
require_once get_template_directory() . '/inc/widgets/social-widget.php';
require_once get_template_directory() . '/inc/widgets/authors-widget.php';
require_once get_template_directory() . '/inc/widgets/advertisement-widget.php';
require_once get_template_directory() . '/inc/widgets/carousel-widget.php';

/**
 * Enregistrer les widgets
 */
function lejournaldesactus_register_widgets() {
    register_widget('Lejournaldesactus_Recent_Posts_Widget');
    register_widget('Lejournaldesactus_Popular_Posts_Widget');
    register_widget('Lejournaldesactus_Newsletter_Widget');
    register_widget('Lejournaldesactus_Social_Widget');
    register_widget('Lejournaldesactus_Authors_Widget');
    register_widget('Lejournaldesactus_Advertisement_Widget');
    register_widget('Lejournaldesactus_Carousel_Widget');
}
add_action('widgets_init', 'lejournaldesactus_register_widgets');

/**
 * Ajouter des scripts pour le media uploader dans les widgets
 */
function lejournaldesactus_admin_enqueue_scripts($hook) {
    if ('widgets.php' !== $hook) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'lejournaldesactus_admin_enqueue_scripts');

/**
 * Traiter l'inscription à la newsletter via AJAX
 */
function lejournaldesactus_newsletter_subscribe_callback() {
    // Vérifier le nonce
    if (!isset($_POST['newsletter_nonce']) || !wp_verify_nonce($_POST['newsletter_nonce'], 'lejournaldesactus_newsletter_nonce')) {
        wp_send_json_error(__('Erreur de sécurité. Veuillez rafraîchir la page et réessayer.', 'lejournaldesactus'));
    }

    // Vérifier l'email
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    if (!is_email($email)) {
        wp_send_json_error(__('Veuillez entrer une adresse email valide.', 'lejournaldesactus'));
    }

    // Ici, vous pouvez intégrer avec votre service de newsletter préféré
    // Par exemple, MailChimp, SendinBlue, etc.
    
    // Pour cet exemple, nous allons simplement enregistrer l'email dans les options WordPress
    $subscribers = get_option('lejournaldesactus_newsletter_subscribers', array());
    
    // Vérifier si l'email existe déjà
    if (in_array($email, $subscribers)) {
        wp_send_json_error(__('Cette adresse email est déjà inscrite à notre newsletter.', 'lejournaldesactus'));
    }
    
    // Ajouter l'email à la liste
    $subscribers[] = $email;
    update_option('lejournaldesactus_newsletter_subscribers', $subscribers);
    
    // Envoyer un email de confirmation (optionnel)
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $subject = sprintf(__('[%s] Confirmation d\'inscription à la newsletter', 'lejournaldesactus'), $site_name);
    $message = sprintf(
        __('Merci de vous être inscrit à la newsletter de %1$s. Vous recevrez nos prochaines actualités à cette adresse email: %2$s', 'lejournaldesactus'),
        $site_name,
        $email
    );
    wp_mail($email, $subject, $message, array('From: ' . $site_name . ' <' . $admin_email . '>'));
    
    // Envoyer une notification à l'administrateur (optionnel)
    $admin_subject = sprintf(__('[%s] Nouvel abonné à la newsletter', 'lejournaldesactus'), $site_name);
    $admin_message = sprintf(
        __('Un nouvel utilisateur s\'est inscrit à la newsletter: %s', 'lejournaldesactus'),
        $email
    );
    wp_mail($admin_email, $admin_subject, $admin_message);
    
    // Retourner un message de succès
    wp_send_json_success(__('Merci pour votre inscription! Vous recevrez bientôt nos actualités.', 'lejournaldesactus'));
}
add_action('wp_ajax_lejournaldesactus_newsletter_subscribe', 'lejournaldesactus_newsletter_subscribe_callback');
add_action('wp_ajax_nopriv_lejournaldesactus_newsletter_subscribe', 'lejournaldesactus_newsletter_subscribe_callback');

/**
 * Ajouter des styles pour les widgets avancés
 */
function lejournaldesactus_widgets_styles() {
    ?>
    <style>
        /* Styles communs pour tous les widgets avancés */
        .widget-advanced {
            margin-bottom: 30px;
        }
        
        /* Widget des articles récents */
        .widget-recent-posts .recent-posts-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .widget-recent-posts .recent-post {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .widget-recent-posts .recent-post:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .widget-recent-posts .recent-post-thumbnail {
            flex: 0 0 80px;
            margin-right: 15px;
        }
        
        .widget-recent-posts .recent-post-thumbnail img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
        
        .widget-recent-posts .recent-post-content {
            flex: 1;
        }
        
        .widget-recent-posts .recent-post-title {
            margin: 0 0 5px;
            font-size: 16px;
            line-height: 1.4;
        }
        
        .widget-recent-posts .recent-post-meta {
            font-size: 12px;
            color: #777;
        }
        
        /* Widget des articles populaires */
        .widget-popular-posts .popular-posts-list {
            counter-reset: popular-counter;
        }
        
        .widget-popular-posts .popular-post {
            position: relative;
            padding-left: 30px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .widget-popular-posts .popular-post:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .widget-popular-posts .popular-post-number {
            position: absolute;
            left: 0;
            top: 0;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .widget-popular-posts .popular-post-title {
            margin: 0 0 5px;
            font-size: 16px;
            line-height: 1.4;
        }
        
        .widget-popular-posts .popular-post-meta {
            font-size: 12px;
            color: #777;
        }
        
        .widget-popular-posts .popular-post-meta span {
            margin-right: 10px;
        }
        
        /* Widget de newsletter */
        .widget-newsletter .newsletter-content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        
        .widget-newsletter .newsletter-text {
            margin-bottom: 15px;
        }
        
        .widget-newsletter .newsletter-form {
            display: flex;
            flex-wrap: wrap;
        }
        
        .widget-newsletter .newsletter-email {
            flex: 1;
            min-width: 70%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
        }
        
        .widget-newsletter .newsletter-submit {
            padding: 10px 15px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        
        .widget-newsletter .newsletter-submit:hover {
            background-color: #555;
        }
        
        .widget-newsletter .newsletter-success {
            margin-top: 10px;
            padding: 10px;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
            border-radius: 4px;
        }
        
        .widget-newsletter .newsletter-error {
            margin-top: 10px;
            padding: 10px;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
            border-radius: 4px;
        }
        
        /* Widget des réseaux sociaux */
        .widget-social .social-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .widget-social .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .widget-social .social-style-circle .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #333;
            color: #fff;
        }
        
        .widget-social .social-style-square .social-link {
            width: 40px;
            height: 40px;
            background-color: #333;
            color: #fff;
        }
        
        .widget-social .social-style-rounded .social-link {
            width: 40px;
            height: 40px;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
        }
        
        .widget-social .social-style-text .social-link {
            color: #333;
            margin-right: 15px;
        }
        
        .widget-social .social-link i {
            font-size: 18px;
        }
        
        .widget-social .social-label {
            margin-left: 5px;
            font-size: 14px;
        }
        
        .widget-social .social-link.facebook:hover {
            background-color: #3b5998;
        }
        
        .widget-social .social-link.twitter:hover {
            background-color: #1da1f2;
        }
        
        .widget-social .social-link.instagram:hover {
            background-color: #e1306c;
        }
        
        .widget-social .social-link.linkedin:hover {
            background-color: #0077b5;
        }
        
        .widget-social .social-link.youtube:hover {
            background-color: #ff0000;
        }
        
        .widget-social .social-link.pinterest:hover {
            background-color: #bd081c;
        }
        
        .widget-social .social-link.tiktok:hover {
            background-color: #000000;
        }
        
        .widget-social .social-style-text .social-link:hover {
            opacity: 0.8;
        }
        
        /* Widget des auteurs */
        .widget-authors .authors-list {
            margin: 0;
            padding: 0;
        }
        
        .widget-authors .author {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .widget-authors .author:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .widget-authors .author-avatar {
            flex: 0 0 60px;
            margin-right: 15px;
        }
        
        .widget-authors .author-avatar img {
            width: 100%;
            height: auto;
            border-radius: 50%;
        }
        
        .widget-authors .author-info {
            flex: 1;
        }
        
        .widget-authors .author-name {
            margin: 0 0 5px;
            font-size: 16px;
            line-height: 1.4;
        }
        
        .widget-authors .author-designation {
            font-size: 13px;
            color: #777;
            margin-bottom: 5px;
        }
        
        .widget-authors .author-posts-count {
            font-size: 12px;
            color: #999;
        }
        
        /* Widget de publicité */
        .widget-advertisement .advertisement-wrap {
            text-align: center;
            position: relative;
        }
        
        .widget-advertisement .advertisement-image {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .widget-advertisement .advertisement-label {
            position: absolute;
            top: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 0 0 0 3px;
        }
        
        .widget-advertisement .ad-size-small {
            max-width: 300px;
            margin: 0 auto;
        }
        
        .widget-advertisement .ad-size-medium {
            max-width: 468px;
            margin: 0 auto;
        }
        
        .widget-advertisement .ad-size-large {
            max-width: 728px;
            margin: 0 auto;
        }
    </style>
    <?php
}
add_action('wp_head', 'lejournaldesactus_widgets_styles');

/**
 * Ajouter les scripts Bootstrap Icons pour les icônes des widgets
 */
function lejournaldesactus_enqueue_widget_scripts() {
    wp_enqueue_style(
        'bootstrap-icons',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
        array(),
        '1.11.0'
    );
}
add_action('wp_enqueue_scripts', 'lejournaldesactus_enqueue_widget_scripts');
