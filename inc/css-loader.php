<?php
/**
 * CSS Loader
 * 
 * Ce fichier gère le chargement centralisé des fichiers CSS du thème.
 * Il permet une meilleure organisation et séparation des préoccupations.
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour gérer le chargement des fichiers CSS
 */
class Lejournaldesactus_CSS_Loader {
    /**
     * Initialisation de la classe
     */
    public function __construct() {
        // Ajouter les styles CSS
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 15);
    }
    
    /**
     * Charger les styles CSS
     */
    public function enqueue_styles() {
        // Styles principaux
        wp_enqueue_style('lejournaldesactus-main', LEJOURNALDESACTUS_THEME_URI . '/assets/css/main.css', array(), LEJOURNALDESACTUS_VERSION);
        
        // Style du mode sombre
        wp_enqueue_style('lejournaldesactus-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/css/dark-mode.css', array('lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
        
        // Style du footer
        wp_enqueue_style('lejournaldesactus-footer', LEJOURNALDESACTUS_THEME_URI . '/assets/css/footer.css', array('lejournaldesactus-main', 'lejournaldesactus-dark-mode'), LEJOURNALDESACTUS_VERSION);
        
        // Style du menu mobile
        wp_enqueue_style('lejournaldesactus-mobile-menu', LEJOURNALDESACTUS_THEME_URI . '/assets/css/mobile-menu.css', array('lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
        
        // Style des articles
        wp_enqueue_style('lejournaldesactus-post', LEJOURNALDESACTUS_THEME_URI . '/assets/css/post.css', array('lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
        
        // Style de la typographie
        wp_enqueue_style('lejournaldesactus-typography', LEJOURNALDESACTUS_THEME_URI . '/assets/css/typography.css', array('lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
        
        // Style des widgets
        wp_enqueue_style('lejournaldesactus-widgets', LEJOURNALDESACTUS_THEME_URI . '/assets/css/widgets.css', array('lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
        
        // Style RGPD
        if (file_exists(LEJOURNALDESACTUS_THEME_DIR . '/assets/css/rgpd.css')) {
            wp_enqueue_style('lejournaldesactus-rgpd', LEJOURNALDESACTUS_THEME_URI . '/assets/css/rgpd.css', array('lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
        }
        
        // Style de la newsletter
        if (file_exists(LEJOURNALDESACTUS_THEME_DIR . '/assets/css/newsletter.css')) {
            wp_enqueue_style('lejournaldesactus-newsletter', LEJOURNALDESACTUS_THEME_URI . '/assets/css/newsletter.css', array('lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
        }
        
        // Style des favoris
        if (file_exists(LEJOURNALDESACTUS_THEME_DIR . '/assets/css/bookmarks.css')) {
            wp_enqueue_style('lejournaldesactus-bookmarks', LEJOURNALDESACTUS_THEME_URI . '/assets/css/bookmarks.css', array('lejournaldesactus-main'), LEJOURNALDESACTUS_VERSION);
        }
        
        // Styles spécifiques à l'admin
        if (is_admin_bar_showing()) {
            wp_enqueue_style('lejournaldesactus-admin-styles', LEJOURNALDESACTUS_THEME_URI . '/assets/css/admin-styles.css', array(), LEJOURNALDESACTUS_VERSION);
        }
    }
}

// Initialiser la classe
$lejournaldesactus_css_loader = new Lejournaldesactus_CSS_Loader();
