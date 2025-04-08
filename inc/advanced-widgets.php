<?php
/**
 * Fonctionnalité de widgets avancés
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Enregistrer les widgets avancés
 */
function lejournaldesactus_register_advanced_widgets() {
    // Enregistrer le widget des articles récents avec image
    register_widget('Lejournaldesactus_Recent_Posts_Widget');
    
    // Enregistrer le widget des articles populaires
    register_widget('Lejournaldesactus_Popular_Posts_Widget');
    
    // Enregistrer le widget de newsletter
    register_widget('Lejournaldesactus_Newsletter_Widget');
    
    // Enregistrer le widget des réseaux sociaux
    register_widget('Lejournaldesactus_Social_Widget');
    
    // Enregistrer le widget des auteurs
    register_widget('Lejournaldesactus_Authors_Widget');
    
    // Enregistrer le widget de publicité
    register_widget('Lejournaldesactus_Ad_Widget');
}
add_action('widgets_init', 'lejournaldesactus_register_advanced_widgets');

/**
 * Ajouter les styles CSS pour les widgets
 */
function lejournaldesactus_widgets_styles() {
    ?>
    <style>
        /* Styles communs pour les widgets avancés */
        .widget-advanced {
            margin-bottom: 30px;
        }
        
        .widget-advanced .widget-title {
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }
        
        .widget-advanced .widget-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: #007bff;
        }
        
        /* Widget des articles récents avec image */
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
            border-radius: 5px;
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
        
        .widget-recent-posts .recent-post-content {
            flex: 1;
        }
        
        .widget-recent-posts .recent-post-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        .widget-recent-posts .recent-post-title a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .widget-recent-posts .recent-post-title a:hover {
            color: #007bff;
        }
        
        .widget-recent-posts .recent-post-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Widget des articles populaires */
        .widget-popular-posts .popular-post {
            position: relative;
            margin-bottom: 15px;
            padding-bottom: 15px;
            padding-left: 25px;
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
            font-size: 1.2rem;
            font-weight: 700;
            color: #007bff;
        }
        
        .widget-popular-posts .popular-post-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        .widget-popular-posts .popular-post-title a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .widget-popular-posts .popular-post-title a:hover {
            color: #007bff;
        }
        
        .widget-popular-posts .popular-post-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Widget de newsletter */
        .widget-newsletter {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
        
        .widget-newsletter .newsletter-text {
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .widget-newsletter .newsletter-form {
            display: flex;
            flex-direction: column;
        }
        
        .widget-newsletter .newsletter-email {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
        
        .widget-newsletter .newsletter-submit {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .widget-newsletter .newsletter-submit:hover {
            background-color: #0056b3;
        }
        
        /* Widget des réseaux sociaux */
        .widget-social .social-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .widget-social .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            margin: 5px;
            border-radius: 50%;
            color: #fff;
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        
        .widget-social .social-link:hover {
            transform: scale(1.1);
        }
        
        .widget-social .social-link.facebook {
            background-color: #3b5998;
        }
        
        .widget-social .social-link.twitter {
            background-color: #1da1f2;
        }
        
        .widget-social .social-link.instagram {
            background: linear-gradient(45deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d);
        }
        
        .widget-social .social-link.linkedin {
            background-color: #0077b5;
        }
        
        .widget-social .social-link.youtube {
            background-color: #ff0000;
        }
        
        /* Widget des auteurs */
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
            border-radius: 50%;
            width: 60px;
            height: 60px;
            object-fit: cover;
        }
        
        .widget-authors .author-info {
            flex: 1;
        }
        
        .widget-authors .author-name {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .widget-authors .author-name a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .widget-authors .author-name a:hover {
            color: #007bff;
        }
        
        .widget-authors .author-posts-count {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Widget de publicité */
        .widget-ad {
            text-align: center;
        }
        
        .widget-ad .ad-image {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        
        .widget-ad .ad-label {
            display: block;
            margin-top: 5px;
            font-size: 0.7rem;
            color: #6c757d;
            text-transform: uppercase;
        }
    </style>
    <?php
}
add_action('wp_head', 'lejournaldesactus_widgets_styles');
