<?php
/**
 * Personnalisation de la page de connexion WordPress
 * 
 * Ce fichier contient les fonctions pour personnaliser la page de connexion
 * avec le style du thème Le Journal des Actus.
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour personnaliser la page de connexion
 */
class Lejournaldesactus_Login_Customizer {
    /**
     * Initialisation de la classe
     */
    public function __construct() {
        // Ajouter les styles et scripts à la page de connexion
        add_action('login_enqueue_scripts', array($this, 'enqueue_login_styles'));
        
        // Modifier l'URL du logo
        add_filter('login_headerurl', array($this, 'custom_login_logo_url'));
        
        // Modifier le titre du logo
        add_filter('login_headertext', array($this, 'custom_login_logo_title'));
        
        // Ajouter un titre au-dessus du formulaire
        add_action('login_form_top', array($this, 'add_login_title'));
    }
    
    /**
     * Ajouter les styles et scripts à la page de connexion
     */
    public function enqueue_login_styles() {
        // Ajouter les polices Google
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', array(), null);
        
        // Ajouter les styles personnalisés
        wp_enqueue_style('lejournaldesactus-login-style', LEJOURNALDESACTUS_THEME_URI . '/assets/css/login-style.css', array('login'), LEJOURNALDESACTUS_VERSION);
    }
    
    /**
     * Modifier l'URL du logo
     */
    public function custom_login_logo_url() {
        return home_url('/');
    }
    
    /**
     * Modifier le titre du logo
     */
    public function custom_login_logo_title() {
        return get_bloginfo('name');
    }
    
    /**
     * Ajouter un titre au-dessus du formulaire
     */
    public function add_login_title() {
        echo '<div class="login-title">Connexion à l\'espace membre</div>';
    }
}

// Initialiser la classe
$lejournaldesactus_login_customizer = new Lejournaldesactus_Login_Customizer();
