<?php
/**
 * Mode sombre/clair
 * 
 * Ce fichier contient les fonctions pour permettre aux utilisateurs
 * de basculer entre le mode sombre et clair, avec sauvegarde des préférences.
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour gérer le mode sombre/clair
 */
class Lejournaldesactus_Dark_Mode {
    /**
     * Initialisation de la classe
     */
    public function __construct() {
        // Ajouter les scripts et styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Ajouter le bouton de basculement dans le menu
        add_filter('wp_nav_menu_items', array($this, 'add_dark_mode_toggle_to_menu'), 10, 2);
        
        // Ajouter les variables CSS pour les couleurs
        add_action('wp_head', array($this, 'add_color_variables'), 5);
        
        // Ajouter l'action AJAX pour les utilisateurs connectés
        add_action('wp_ajax_lejournaldesactus_save_theme_preference', array($this, 'save_theme_preference'));
        add_action('wp_ajax_nopriv_lejournaldesactus_save_theme_preference', array($this, 'save_theme_preference_guest'));
    }
    
    /**
     * Ajouter les scripts et styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style('lejournaldesactus-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/css/dark-mode.css', array(), LEJOURNALDESACTUS_VERSION);
        wp_enqueue_script('lejournaldesactus-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/js/dark-mode.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
        
        wp_localize_script('lejournaldesactus-dark-mode', 'lejournaldesactusDarkMode', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lejournaldesactus_dark_mode_nonce'),
            'darkModeText' => __('Mode sombre', 'lejournaldesactus'),
            'lightModeText' => __('Mode clair', 'lejournaldesactus'),
            'autoModeText' => __('Mode auto', 'lejournaldesactus'),
            'toggleText' => __('Changer de thème', 'lejournaldesactus'),
        ));
    }
    
    /**
     * Ajouter le bouton de basculement dans le header
     */
    public function add_toggle_button() {
        // Suppression du bouton flottant qui n'est pas nécessaire
        // Le basculement se fera uniquement via le menu
    }
    
    /**
     * Ajouter le bouton de basculement dans le menu
     */
    public function add_dark_mode_toggle_to_menu($items, $args) {
        if ($args->theme_location == 'primary') {
            $toggle_item = '<li class="menu-item menu-item-dark-mode">';
            $toggle_item .= '<a href="#" class="dark-mode-menu-toggle">';
            $toggle_item .= '<i class="bi bi-circle-half"></i> <span class="dark-mode-text">' . __('Thème', 'lejournaldesactus') . '</span>';
            $toggle_item .= '</a></li>';
            
            $items .= $toggle_item;
        }
        
        return $items;
    }
    
    /**
     * Ajouter les variables CSS pour les couleurs
     */
    public function add_color_variables() {
        ?>
        <style>
            /* Forcer le mode sombre sur tout le document */
            [data-theme="dark"] {
                background-color: #121212 !important;
                color: #e0e0e0 !important;
            }
            
            [data-theme="dark"] body,
            [data-theme="dark"] html,
            [data-theme="dark"] #page,
            [data-theme="dark"] .site,
            [data-theme="dark"] .site-content,
            [data-theme="dark"] .content-area,
            [data-theme="dark"] #content,
            [data-theme="dark"] #main,
            [data-theme="dark"] .container,
            [data-theme="dark"] .row,
            [data-theme="dark"] .col {
                color: #e0e0e0 !important;
            }
            
            /* Forcer les couleurs pour les titres du site */
            [data-theme="dark"] .site-title,
            [data-theme="dark"] .site-title a,
            [data-theme="dark"] h1.site-title,
            [data-theme="dark"] h1.site-title a,
            [data-theme="dark"] .site-name,
            [data-theme="dark"] .site-name a,
            [data-theme="dark"] .navbar-brand,
            [data-theme="dark"] .navbar-brand a,
            [data-theme="dark"] #masthead *,
            [data-theme="dark"] .site-branding *,
            [data-theme="dark"] .sitename,
            [data-theme="dark"] .sitename a,
            [data-theme="dark"] #sitename,
            [data-theme="dark"] #sitename a,
            [data-theme="dark"] .site-header .sitename,
            [data-theme="dark"] .site-header .sitename a,
            [data-theme="dark"] header .sitename,
            [data-theme="dark"] header .sitename a,
            [data-theme="dark"] .navbar .sitename,
            [data-theme="dark"] .navbar .sitename a {
                color: #ffffff !important;
            }
            
            /* Forcer les couleurs pour le footer et la newsletter */
            [data-theme="dark"] footer,
            [data-theme="dark"] .footer,
            [data-theme="dark"] #footer,
            [data-theme="dark"] .site-footer,
            [data-theme="dark"] #site-footer {
                background-color: #1a1a1a !important;
                color: #e0e0e0 !important;
            }
            
            [data-theme="dark"] footer .site-title,
            [data-theme="dark"] footer .site-title a,
            [data-theme="dark"] .footer .site-title,
            [data-theme="dark"] .footer .site-title a,
            [data-theme="dark"] #footer .site-title,
            [data-theme="dark"] #footer .site-title a,
            [data-theme="dark"] .site-footer .site-title,
            [data-theme="dark"] .site-footer .site-title a,
            [data-theme="dark"] #site-footer .site-title,
            [data-theme="dark"] #site-footer .site-title a,
            [data-theme="dark"] footer h1,
            [data-theme="dark"] footer h2,
            [data-theme="dark"] footer h3,
            [data-theme="dark"] footer h4,
            [data-theme="dark"] footer h5,
            [data-theme="dark"] footer h6,
            [data-theme="dark"] .footer h1,
            [data-theme="dark"] .footer h2,
            [data-theme="dark"] .footer h3,
            [data-theme="dark"] .footer h4,
            [data-theme="dark"] .footer h5,
            [data-theme="dark"] .footer h6,
            [data-theme="dark"] #footer h1,
            [data-theme="dark"] #footer h2,
            [data-theme="dark"] #footer h3,
            [data-theme="dark"] #footer h4,
            [data-theme="dark"] #footer h5,
            [data-theme="dark"] #footer h6 {
                color: #f5f5f5 !important;
            }
            
            [data-theme="dark"] .newsletter,
            [data-theme="dark"] .newsletter-section,
            [data-theme="dark"] .newsletter-container,
            [data-theme="dark"] .newsletter-wrapper,
            [data-theme="dark"] .newsletter-block,
            [data-theme="dark"] .newsletter-widget,
            [data-theme="dark"] .widget_newsletter,
            [data-theme="dark"] .widget-newsletter,
            [data-theme="dark"] #newsletter,
            [data-theme="dark"] .subscribe,
            [data-theme="dark"] .subscribe-section,
            [data-theme="dark"] .subscribe-container,
            [data-theme="dark"] .subscribe-wrapper,
            [data-theme="dark"] .subscribe-block,
            [data-theme="dark"] .subscribe-widget,
            [data-theme="dark"] .widget_subscribe,
            [data-theme="dark"] .widget-subscribe,
            [data-theme="dark"] #subscribe,
            [data-theme="dark"] div[class*="newsletter"],
            [data-theme="dark"] div[id*="newsletter"],
            [data-theme="dark"] div[class*="subscribe"],
            [data-theme="dark"] div[id*="subscribe"] {
                background-color: #1e1e1e !important;
                color: #e0e0e0 !important;
                border-color: #333333 !important;
            }
            
            [data-theme="dark"] .newsletter *,
            [data-theme="dark"] .newsletter-section *,
            [data-theme="dark"] .subscribe *,
            [data-theme="dark"] .subscribe-section * {
                color: #e0e0e0 !important;
            }
            
            [data-theme="dark"] .newsletter h1,
            [data-theme="dark"] .newsletter h2,
            [data-theme="dark"] .newsletter h3,
            [data-theme="dark"] .newsletter h4,
            [data-theme="dark"] .newsletter h5,
            [data-theme="dark"] .newsletter h6,
            [data-theme="dark"] .newsletter-section h1,
            [data-theme="dark"] .newsletter-section h2,
            [data-theme="dark"] .newsletter-section h3,
            [data-theme="dark"] .newsletter-section h4,
            [data-theme="dark"] .newsletter-section h5,
            [data-theme="dark"] .newsletter-section h6,
            [data-theme="dark"] .subscribe h1,
            [data-theme="dark"] .subscribe h2,
            [data-theme="dark"] .subscribe h3,
            [data-theme="dark"] .subscribe h4,
            [data-theme="dark"] .subscribe h5,
            [data-theme="dark"] .subscribe h6,
            [data-theme="dark"] .subscribe-section h1,
            [data-theme="dark"] .subscribe-section h2,
            [data-theme="dark"] .subscribe-section h3,
            [data-theme="dark"] .subscribe-section h4,
            [data-theme="dark"] .subscribe-section h5,
            [data-theme="dark"] .subscribe-section h6 {
                color: #f5f5f5 !important;
            }
            
            :root {
                /* Variables pour le mode clair */
                --body-bg: #ffffff;
                --text-color: #212529;
                --heading-color: #212529;
                --link-color: #007bff;
                --link-hover-color: #0056b3;
                --card-bg:rgb(243, 245, 245)
                --card-border: #dee2e6;
                --input-bg: #ffffff;
                --input-border: #ced4da;
                --input-text: #495057;
                --button-primary-bg: #007bff;
                --button-primary-text: #ffffff;
                --button-secondary-bg: #6c757d;
                --button-secondary-text: #ffffff;
                --header-bg: #ffffff;
                --header-text: #212529;
                --footer-bg: #f8f9fa;
                --footer-text: #212529;
                --sidebar-bg: #f8f9fa;
                --sidebar-text: #212529;
                --border-color: #dee2e6;
                --shadow-color: rgba(0, 0, 0, 0.1);
                --blockquote-bg: #f8f9fa;
                --blockquote-border: #dee2e6;
                --code-bg: #f8f9fa;
                --code-text: #e83e8c;
                --table-header-bg: #f8f9fa;
                --table-border: #dee2e6;
                --table-bg: #ffffff;
                --nav-bg: #ffffff;
                --nav-text: #212529;
                --nav-hover-bg: #f8f9fa;
                --nav-hover-text: #007bff;
                --dropdown-bg: #ffffff;
                --dropdown-text: #212529;
                --dropdown-hover-bg: #f8f9fa;
                --dropdown-hover-text: #007bff;
                --dropdown-border: #dee2e6;
            }
            
            [data-theme="dark"] {
                /* Variables pour le mode sombre */
                --body-bg: #121212;
                --text-color: #e0e0e0;
                --heading-color: #f5f5f5;
                --link-color: #4fc3f7;
                --link-hover-color: #29b6f6;
                --card-bg: #1e1e1e;
                --card-border: #333333;
                --input-bg: #2d2d2d;
                --input-border: #444444;
                --input-text: #e0e0e0;
                --button-primary-bg: #5c6bc0;
                --button-primary-text: #ffffff;
                --button-secondary-bg: #546e7a;
                --button-secondary-text: #ffffff;
                --header-bg: #1e1e1e;
                --header-text: #e0e0e0;
                --footer-bg: #1e1e1e;
                --footer-text: #e0e0e0;
                --sidebar-bg: #1e1e1e;
                --sidebar-text: #e0e0e0;
                --border-color: #333333;
                --shadow-color: rgba(0, 0, 0, 0.5);
                --blockquote-bg: #2d2d2d;
                --blockquote-border: #444444;
                --code-bg: #2d2d2d;
                --code-text: #f48fb1;
                --table-header-bg: #2d2d2d;
                --table-border: #444444;
                --table-bg: #1e1e1e;
                --nav-bg: #1e1e1e;
                --nav-text: #e0e0e0;
                --nav-hover-bg: #2d2d2d;
                --nav-hover-text: #4fc3f7;
                --dropdown-bg: #1e1e1e;
                --dropdown-text: #e0e0e0;
                --dropdown-hover-bg: #2d2d2d;
                --dropdown-hover-text: #4fc3f7;
                --dropdown-border: #444444;
            }
            
            /* Transition douce entre les modes */
            body {
                transition: background-color 0.3s ease, color 0.3s ease;
            }
            
            /* Styles de base qui utilisent les variables */
            body {
                background-color: var(--body-bg);
                color: var(--text-color);
            }
            
            h1, h2, h3, h4, h5, h6 {
                color: var(--heading-color);
            }
            
            a {
                color: var(--link-color);
            }
            
            a:hover {
                color: var(--link-hover-color);
            }
            
            .card, .widget, article {
                background-color: var(--card-bg);
                border-color: var(--card-border);
            }
            
            input, textarea, select {
                background-color: var(--input-bg);
                border-color: var(--input-border);
                color: var(--input-text);
            }
            
            .btn-primary {
                background-color: var(--button-primary-bg);
                color: var(--button-primary-text);
            }
            
            .btn-secondary {
                background-color: var(--button-secondary-bg);
                color: var(--button-secondary-text);
            }
            
            header {
                background-color: var(--header-bg);
                color: var(--header-text);
            }
            
            footer {
                background-color: var(--footer-bg);
                color: var(--footer-text);
            }
            
            .sidebar {
                background-color: var(--sidebar-bg);
                color: var(--sidebar-text);
            }
            
            /* Forcer les couleurs des boutons de favoris */
            [data-theme="dark"] .bookmark-button,
            [data-theme="dark"] .bookmark-icon,
            [data-theme="dark"] .bookmark-link,
            [data-theme="dark"] .bookmark-toggle,
            [data-theme="dark"] .add-bookmark,
            [data-theme="dark"] .remove-bookmark {
                background-color: transparent !important;
                color: #e0e0e0 !important;
                border-color: #444444 !important;
            }
            
            [data-theme="dark"] .bookmark-button.bookmarked,
            [data-theme="dark"] .bookmark-icon.bookmarked,
            [data-theme="dark"] .bookmark-link.bookmarked,
            [data-theme="dark"] .bookmark-toggle.bookmarked,
            [data-theme="dark"] .add-bookmark.bookmarked,
            [data-theme="dark"] .remove-bookmark.bookmarked {
                background-color: #2d2d2d !important; 
                color: #4fc3f7 !important;
                border-color: #4fc3f7 !important;
            }
            
            /* Styles pour la section CTA et newsletter */
            [data-theme="dark"] #cta,
            [data-theme="dark"] .cta,
            [data-theme="dark"] .cta-section,
            [data-theme="dark"] #cta .newsletter,
            [data-theme="dark"] #cta .newsletter form,
            [data-theme="dark"] .cta .newsletter,
            [data-theme="dark"] .cta .newsletter form,
            [data-theme="dark"] .cta-section .newsletter,
            [data-theme="dark"] .cta-section .newsletter form {
                background-color: #1e1e1e !important;
                color: #e0e0e0 !important;
                border-color: #333333 !important;
            }
            
            [data-theme="dark"] #cta form,
            [data-theme="dark"] .cta form,
            [data-theme="dark"] .cta-section form,
            [data-theme="dark"] #cta .newsletter form,
            [data-theme="dark"] .cta .newsletter form,
            [data-theme="dark"] .cta-section .newsletter form {
                background-color: #1e1e1e !important;
                border-color: #333333 !important;
            }
            
            [data-theme="dark"] #cta input,
            [data-theme="dark"] .cta input,
            [data-theme="dark"] .cta-section input,
            [data-theme="dark"] #cta .form-control,
            [data-theme="dark"] .cta .form-control,
            [data-theme="dark"] .cta-section .form-control {
                background-color: #2d2d2d !important;
                color: #e0e0e0 !important;
                border-color: #444444 !important;
            }
            
            /* Styles spécifiques pour les images en mode sombre */
            [data-theme="dark"] img:not(.no-dark-filter) {
                filter: brightness(0.9);
            }
            
            /* Sélecteurs supplémentaires pour s'assurer que tous les textes changent de couleur en mode sombre */
            [data-theme="dark"] p,
            [data-theme="dark"] span,
            [data-theme="dark"] div,
            [data-theme="dark"] li,
            [data-theme="dark"] td,
            [data-theme="dark"] th,
            [data-theme="dark"] label,
            [data-theme="dark"] blockquote,
            [data-theme="dark"] figcaption,
            [data-theme="dark"] .entry-content,
            [data-theme="dark"] .entry-title,
            [data-theme="dark"] .widget-title,
            [data-theme="dark"] .post-title,
            [data-theme="dark"] .post-content,
            [data-theme="dark"] .post-meta,
            [data-theme="dark"] .comment-content,
            [data-theme="dark"] .comment-author,
            [data-theme="dark"] .comment-metadata,
            [data-theme="dark"] .site-title,
            [data-theme="dark"] .site-description,
            [data-theme="dark"] .nav-link,
            [data-theme="dark"] .dropdown-item,
            [data-theme="dark"] .menu-item a {
                color: #e0e0e0 !important;
            }
            
            [data-theme="dark"] h1,
            [data-theme="dark"] h2,
            [data-theme="dark"] h3,
            [data-theme="dark"] h4,
            [data-theme="dark"] h5,
            [data-theme="dark"] h6,
            [data-theme="dark"] .h1,
            [data-theme="dark"] .h2,
            [data-theme="dark"] .h3,
            [data-theme="dark"] .h4,
            [data-theme="dark"] .h5,
            [data-theme="dark"] .h6 {
                color: var(--heading-color);
            }
            
            [data-theme="dark"] a:not(.btn):not(.nav-link):not(.dropdown-item) {
                color: var(--link-color);
            }
            
            [data-theme="dark"] a:hover:not(.btn):not(.nav-link):not(.dropdown-item) {
                color: var(--link-hover-color);
            }
            
            /* Améliorer la lisibilité des placeholders */
            [data-theme="dark"] input::placeholder,
            [data-theme="dark"] textarea::placeholder,
            [data-theme="dark"] select::placeholder,
            [data-theme="dark"] .form-control::placeholder {
                color: #aaaaaa !important;
                opacity: 1 !important;
            }
            
            [data-theme="dark"] input::-webkit-input-placeholder,
            [data-theme="dark"] textarea::-webkit-input-placeholder,
            [data-theme="dark"] select::-webkit-input-placeholder,
            [data-theme="dark"] .form-control::-webkit-input-placeholder {
                color: #aaaaaa !important;
                opacity: 1 !important;
            }
            
            [data-theme="dark"] input::-moz-placeholder,
            [data-theme="dark"] textarea::-moz-placeholder,
            [data-theme="dark"] select::-moz-placeholder,
            [data-theme="dark"] .form-control::-moz-placeholder {
                color: #aaaaaa !important;
                opacity: 1 !important;
            }
        </style>
        <?php
    }
    
    /**
     * Sauvegarder la préférence de thème (utilisateurs connectés)
     */
    public function save_theme_preference() {
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'lejournaldesactus_dark_mode_nonce')) {
            wp_send_json_error(__('Erreur de sécurité. Veuillez rafraîchir la page et réessayer.', 'lejournaldesactus'));
        }
        
        // Vérifier le thème
        if (!isset($_POST['theme']) || !in_array($_POST['theme'], array('light', 'dark', 'auto'))) {
            wp_send_json_error(__('Thème invalide.', 'lejournaldesactus'));
        }
        
        $theme = sanitize_text_field($_POST['theme']);
        $user_id = get_current_user_id();
        
        // Mettre à jour la préférence
        update_user_meta($user_id, 'lejournaldesactus_theme_preference', $theme);
        
        wp_send_json_success(array(
            'message' => __('Préférence de thème sauvegardée.', 'lejournaldesactus'),
            'theme' => $theme
        ));
    }
    
    /**
     * Sauvegarder la préférence de thème (visiteurs)
     */
    public function save_theme_preference_guest() {
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'lejournaldesactus_dark_mode_nonce')) {
            wp_send_json_error(__('Erreur de sécurité. Veuillez rafraîchir la page et réessayer.', 'lejournaldesactus'));
        }
        
        // Vérifier le thème
        if (!isset($_POST['theme']) || !in_array($_POST['theme'], array('light', 'dark', 'auto'))) {
            wp_send_json_error(__('Thème invalide.', 'lejournaldesactus'));
        }
        
        $theme = sanitize_text_field($_POST['theme']);
        
        // Pas besoin de faire quoi que ce soit côté serveur pour les visiteurs
        // car le cookie est géré côté client dans le JavaScript
        
        wp_send_json_success(array(
            'message' => __('Préférence de thème sauvegardée.', 'lejournaldesactus'),
            'theme' => $theme
        ));
    }
}

// Initialiser la classe
$lejournaldesactus_dark_mode = new Lejournaldesactus_Dark_Mode();
