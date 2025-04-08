<?php
/**
 * Fonctions de sécurité du thème
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Ajouter des en-têtes de sécurité
 */
function lejournaldesactus_add_security_headers($headers) {
    $headers['X-Content-Type-Options'] = 'nosniff';
    $headers['X-XSS-Protection'] = '1; mode=block';
    $headers['X-Frame-Options'] = 'SAMEORIGIN';
    $headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
    
    return $headers;
}

/**
 * Désactiver la divulgation de la version de WordPress
 */
function lejournaldesactus_remove_version() {
    return '';
}
add_filter('the_generator', 'lejournaldesactus_remove_version');

/**
 * Supprimer les balises meta inutiles
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

/**
 * Sécuriser les formulaires avec nonce
 */
function lejournaldesactus_add_nonce_to_forms() {
    wp_nonce_field('lejournaldesactus_form_action', 'lejournaldesactus_form_nonce');
}

/**
 * Vérifier le nonce des formulaires
 */
function lejournaldesactus_verify_nonce($nonce_field = 'lejournaldesactus_form_nonce', $action = 'lejournaldesactus_form_action') {
    if (!isset($_POST[$nonce_field]) || !wp_verify_nonce($_POST[$nonce_field], $action)) {
        die('Vérification de sécurité échouée. Veuillez réessayer.');
    }
    return true;
}

/**
 * Désactiver l'exécution de PHP dans les dossiers uploads
 */
function lejournaldesactus_disable_php_in_uploads($rules) {
    $new_rules = "\n# Désactiver l'exécution de PHP dans le dossier uploads\n";
    $new_rules .= "<Directory " . wp_upload_dir()['basedir'] . ">\n";
    $new_rules .= "    <Files *.php>\n";
    $new_rules .= "        Order Deny,Allow\n";
    $new_rules .= "        Deny from All\n";
    $new_rules .= "    </Files>\n";
    $new_rules .= "</Directory>\n";
    
    return $rules . $new_rules;
}
add_filter('mod_rewrite_rules', 'lejournaldesactus_disable_php_in_uploads');

/**
 * Sécuriser les données des utilisateurs
 */
function lejournaldesactus_sanitize_data($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = lejournaldesactus_sanitize_data($value);
        }
    } else {
        // Supprimer les balises HTML et PHP
        $data = strip_tags($data);
        
        // Échapper les caractères spéciaux SQL
        $data = esc_sql($data);
        
        // Échapper les attributs HTML
        $data = esc_attr($data);
    }
    
    return $data;
}

/**
 * Limiter les tentatives de connexion échouées
 */
function lejournaldesactus_limit_login_attempts($user, $username, $password) {
    if (empty($username)) {
        return $user;
    }
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $failed_login_limit = 5; // Nombre maximum de tentatives
    $lockout_duration = 15 * MINUTE_IN_SECONDS; // Durée du verrouillage en secondes
    
    // Récupérer les tentatives échouées
    $failed_login = get_transient('failed_login_' . $ip);
    
    if ($failed_login) {
        // Vérifier si l'utilisateur est verrouillé
        if ($failed_login['lockout'] && time() < $failed_login['lockout']) {
            // Calculer le temps restant
            $time_left = $failed_login['lockout'] - time();
            $minutes_left = ceil($time_left / 60);
            
            return new WP_Error('too_many_attempts', sprintf(__('Trop de tentatives de connexion échouées. Veuillez réessayer dans %d minutes.', 'lejournaldesactus'), $minutes_left));
        }
    }
    
    return $user;
}
add_filter('authenticate', 'lejournaldesactus_limit_login_attempts', 30, 3);

/**
 * Enregistrer les tentatives de connexion échouées
 */
function lejournaldesactus_failed_login($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $failed_login_limit = 5; // Nombre maximum de tentatives
    $lockout_duration = 15 * MINUTE_IN_SECONDS; // Durée du verrouillage en secondes
    
    // Récupérer les tentatives échouées
    $failed_login = get_transient('failed_login_' . $ip);
    
    if (!$failed_login) {
        $failed_login = array('count' => 0, 'lockout' => false);
    }
    
    $failed_login['count']++;
    
    if ($failed_login['count'] >= $failed_login_limit) {
        $failed_login['lockout'] = time() + $lockout_duration;
    }
    
    set_transient('failed_login_' . $ip, $failed_login, DAY_IN_SECONDS);
}
add_action('wp_login_failed', 'lejournaldesactus_failed_login');

/**
 * Réinitialiser le compteur de tentatives après une connexion réussie
 */
function lejournaldesactus_successful_login($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient('failed_login_' . $ip);
}
add_action('wp_login', 'lejournaldesactus_successful_login');
