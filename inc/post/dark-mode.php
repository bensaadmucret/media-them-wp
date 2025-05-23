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
        // Ajouter les scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Ajouter l'action AJAX pour les utilisateurs connectés
        add_action('wp_ajax_lejournaldesactus_save_theme_preference', array($this, 'save_theme_preference'));
        add_action('wp_ajax_nopriv_lejournaldesactus_save_theme_preference', array($this, 'save_theme_preference_guest'));
    }
    
    /**
     * Ajouter les scripts
     */
    public function enqueue_scripts() {
        // Les styles CSS sont maintenant gérés dans le fichier css-loader.php
        
        // Scripts JS pour le mode sombre
        if (get_theme_mod('lejournaldesactus_enable_dark_mode', true)) {
            wp_enqueue_script('lejournaldesactus-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/js/dark-mode.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
        }
        wp_enqueue_script('lejournaldesactus-bootstrap-dark-mode', LEJOURNALDESACTUS_THEME_URI . '/assets/js/bootstrap-dark-mode.js', array('jquery'), LEJOURNALDESACTUS_VERSION, true);
        
        wp_localize_script('lejournaldesactus-dark-mode', 'lejournaldesactusDarkMode', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lejournaldesactus_dark_mode_nonce'),
            'darkModeText' => __('Mode sombre', 'lejournaldesactus'),
            'lightModeText' => __('Mode clair', 'lejournaldesactus'),
            'autoModeText' => __('Mode auto', 'lejournaldesactus'),
            'toggleText' => __('Changer de thème', 'lejournaldesactus'),
            'loggedIn' => is_user_logged_in(),
        ));
        
        // Localiser le script bootstrap-dark-mode également
        wp_localize_script('lejournaldesactus-bootstrap-dark-mode', 'lejournaldesactusDarkMode', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lejournaldesactus_dark_mode_nonce'),
            'loggedIn' => is_user_logged_in(),
        ));
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
     * @deprecated Tous les styles ont été déplacés dans le fichier dark-mode.css
     */
    public function add_color_variables() {
        // Cette fonction est désactivée car tous les styles ont été déplacés dans le fichier CSS
        return;
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

/**
 * Vérifier si le mode sombre est actif
 * 
 * @return bool True si le mode sombre est actif, false sinon
 */
if (!function_exists('is_dark_mode_active')) {
function is_dark_mode_active() {
    // Vérifier si le cookie existe
    if (isset($_COOKIE['lejournaldesactus_theme']) && $_COOKIE['lejournaldesactus_theme'] === 'dark') {
        return true;
    }
    
    // Vérifier si l'utilisateur est connecté et a une préférence
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $theme = get_user_meta($user_id, 'lejournaldesactus_theme_preference', true);
        
        if ($theme === 'dark') {
            return true;
        }
    }
    
    return false;
}
}
