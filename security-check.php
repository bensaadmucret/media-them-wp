<?php
/**
 * Le Journal des Actus Security Check
 * 
 * Ce fichier permet de vérifier la sécurité du thème WordPress.
 * À utiliser uniquement en environnement de développement.
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit('Accès direct au script interdit');
}

/**
 * Classe de vérification de sécurité
 */
class LeJournalDesActus_Security_Check {
    
    /**
     * Exécuter toutes les vérifications de sécurité
     */
    public static function run_all_checks() {
        self::check_file_permissions();
        self::check_wp_config();
        self::check_debug_mode();
        self::check_plugins();
        self::check_theme_files();
        self::check_user_roles();
        self::check_database_prefix();
        self::check_ssl();
    }
    
    /**
     * Vérifier les permissions des fichiers
     */
    public static function check_file_permissions() {
        echo '<h3>Vérification des permissions des fichiers</h3>';
        
        $wp_content_dir = WP_CONTENT_DIR;
        $upload_dir = wp_upload_dir();
        
        $directories = array(
            ABSPATH => '0755',
            $wp_content_dir => '0755',
            $upload_dir['basedir'] => '0755',
            get_template_directory() => '0755'
        );
        
        foreach ($directories as $directory => $recommended_perm) {
            $current_perm = substr(sprintf('%o', fileperms($directory)), -4);
            $status = ($current_perm <= $recommended_perm) ? 'OK' : 'ATTENTION';
            
            echo "<p>$directory : Permission actuelle : $current_perm, Recommandée : $recommended_perm - <strong>$status</strong></p>";
        }
    }
    
    /**
     * Vérifier la sécurité du fichier wp-config.php
     */
    public static function check_wp_config() {
        echo '<h3>Vérification du fichier wp-config.php</h3>';
        
        $wp_config_path = ABSPATH . 'wp-config.php';
        
        if (file_exists($wp_config_path)) {
            $wp_config_content = file_get_contents($wp_config_path);
            
            $checks = array(
                'AUTH_KEY' => "define('AUTH_KEY',",
                'SECURE_AUTH_KEY' => "define('SECURE_AUTH_KEY',",
                'LOGGED_IN_KEY' => "define('LOGGED_IN_KEY',",
                'NONCE_KEY' => "define('NONCE_KEY',",
                'AUTH_SALT' => "define('AUTH_SALT',",
                'SECURE_AUTH_SALT' => "define('SECURE_AUTH_SALT',",
                'LOGGED_IN_SALT' => "define('LOGGED_IN_SALT',",
                'NONCE_SALT' => "define('NONCE_SALT',"
            );
            
            foreach ($checks as $key => $check) {
                $status = (strpos($wp_config_content, $check) !== false) ? 'OK' : 'ATTENTION';
                echo "<p>Clé $key : <strong>$status</strong></p>";
            }
            
            // Vérifier si le préfixe de table est personnalisé
            $default_prefix = "wp_";
            $prefix_check = (strpos($wp_config_content, "\$table_prefix = '$default_prefix'") === false) ? 'OK' : 'ATTENTION';
            echo "<p>Préfixe de table personnalisé : <strong>$prefix_check</strong></p>";
            
            // Vérifier si le mode debug est activé
            $debug_check = (strpos($wp_config_content, "define('WP_DEBUG', true)") === false) ? 'OK' : 'ATTENTION';
            echo "<p>Mode debug désactivé en production : <strong>$debug_check</strong></p>";
        } else {
            echo "<p>Impossible de trouver le fichier wp-config.php</p>";
        }
    }
    
    /**
     * Vérifier le mode debug
     */
    public static function check_debug_mode() {
        echo '<h3>Vérification du mode debug</h3>';
        
        $debug_status = (defined('WP_DEBUG') && WP_DEBUG) ? 'ACTIVÉ' : 'DÉSACTIVÉ';
        $status = ($debug_status === 'DÉSACTIVÉ') ? 'OK' : 'ATTENTION';
        
        echo "<p>Mode debug : $debug_status - <strong>$status</strong></p>";
        
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            echo "<p>Debug log : ACTIVÉ - <strong>ATTENTION</strong></p>";
        } else {
            echo "<p>Debug log : DÉSACTIVÉ - <strong>OK</strong></p>";
        }
        
        if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) {
            echo "<p>Debug display : ACTIVÉ - <strong>ATTENTION</strong></p>";
        } else {
            echo "<p>Debug display : DÉSACTIVÉ - <strong>OK</strong></p>";
        }
    }
    
    /**
     * Vérifier les plugins installés
     */
    public static function check_plugins() {
        echo '<h3>Vérification des plugins</h3>';
        
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', array());
        
        $security_plugins = array(
            'wordfence' => 'Wordfence Security',
            'all-in-one-wp-security-and-firewall' => 'All In One WP Security & Firewall',
            'sucuri-scanner' => 'Sucuri Security',
            'better-wp-security' => 'iThemes Security',
            'wp-security-audit-log' => 'WP Security Audit Log'
        );
        
        $found_security_plugins = array();
        
        foreach ($all_plugins as $plugin_path => $plugin_data) {
            foreach ($security_plugins as $plugin_slug => $plugin_name) {
                if (strpos($plugin_path, $plugin_slug) !== false && in_array($plugin_path, $active_plugins)) {
                    $found_security_plugins[] = $plugin_name;
                }
            }
        }
        
        if (count($found_security_plugins) > 0) {
            echo "<p>Plugins de sécurité actifs : " . implode(', ', $found_security_plugins) . " - <strong>OK</strong></p>";
        } else {
            echo "<p>Aucun plugin de sécurité actif - <strong>ATTENTION</strong></p>";
        }
        
        // Vérifier les plugins obsolètes
        foreach ($all_plugins as $plugin_path => $plugin_data) {
            if (in_array($plugin_path, $active_plugins)) {
                $last_updated = strtotime($plugin_data['Last updated']);
                $six_months_ago = strtotime('-6 months');
                
                if ($last_updated < $six_months_ago) {
                    echo "<p>Plugin obsolète : {$plugin_data['Name']} (Dernière mise à jour : {$plugin_data['Last updated']}) - <strong>ATTENTION</strong></p>";
                }
            }
        }
    }
    
    /**
     * Vérifier les fichiers du thème
     */
    public static function check_theme_files() {
        echo '<h3>Vérification des fichiers du thème</h3>';
        
        $theme_dir = get_template_directory();
        $files_to_check = array(
            'functions.php',
            'header.php',
            'footer.php',
            'index.php',
            'single.php',
            'page.php',
            'sidebar.php',
            'comments.php'
        );
        
        $patterns_to_check = array(
            'eval(' => 'Fonction eval() détectée',
            'base64_decode(' => 'Fonction base64_decode() détectée',
            'system(' => 'Fonction system() détectée',
            'exec(' => 'Fonction exec() détectée',
            'shell_exec(' => 'Fonction shell_exec() détectée',
            'passthru(' => 'Fonction passthru() détectée',
            'preg_replace' => 'Fonction preg_replace() détectée (vérifier l\'utilisation du modificateur /e)',
            'create_function(' => 'Fonction create_function() détectée',
            'include($_' => 'Inclusion dynamique détectée',
            'require($_' => 'Inclusion dynamique détectée',
            'include_once($_' => 'Inclusion dynamique détectée',
            'require_once($_' => 'Inclusion dynamique détectée',
            '$_REQUEST' => 'Utilisation de $_REQUEST détectée (utiliser $_GET ou $_POST spécifiquement)',
            '<?=' => 'Short tag PHP détecté'
        );
        
        foreach ($files_to_check as $file) {
            $file_path = $theme_dir . '/' . $file;
            
            if (file_exists($file_path)) {
                $content = file_get_contents($file_path);
                $issues_found = false;
                
                foreach ($patterns_to_check as $pattern => $message) {
                    if (strpos($content, $pattern) !== false) {
                        echo "<p>$file : $message - <strong>ATTENTION</strong></p>";
                        $issues_found = true;
                    }
                }
                
                if (!$issues_found) {
                    echo "<p>$file : Aucun problème détecté - <strong>OK</strong></p>";
                }
            } else {
                echo "<p>$file : Fichier non trouvé - <strong>ATTENTION</strong></p>";
            }
        }
    }
    
    /**
     * Vérifier les rôles utilisateurs
     */
    public static function check_user_roles() {
        echo '<h3>Vérification des rôles utilisateurs</h3>';
        
        $users = count_users();
        
        echo "<p>Nombre total d'utilisateurs : {$users['total_users']}</p>";
        
        foreach ($users['avail_roles'] as $role => $count) {
            $status = ($role === 'administrator' && $count > 1) ? 'ATTENTION' : 'OK';
            echo "<p>Rôle $role : $count utilisateur(s) - <strong>$status</strong></p>";
        }
    }
    
    /**
     * Vérifier le préfixe de la base de données
     */
    public static function check_database_prefix() {
        global $wpdb;
        
        echo '<h3>Vérification du préfixe de la base de données</h3>';
        
        $prefix = $wpdb->prefix;
        $status = ($prefix === 'wp_') ? 'ATTENTION' : 'OK';
        
        echo "<p>Préfixe de la base de données : $prefix - <strong>$status</strong></p>";
    }
    
    /**
     * Vérifier l'utilisation de SSL
     */
    public static function check_ssl() {
        echo '<h3>Vérification de SSL</h3>';
        
        $is_ssl = is_ssl();
        $status = $is_ssl ? 'OK' : 'ATTENTION';
        
        echo "<p>Utilisation de SSL : " . ($is_ssl ? 'Oui' : 'Non') . " - <strong>$status</strong></p>";
        
        $force_ssl_admin = defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN;
        $admin_status = $force_ssl_admin ? 'OK' : 'ATTENTION';
        
        echo "<p>SSL forcé pour l'administration : " . ($force_ssl_admin ? 'Oui' : 'Non') . " - <strong>$admin_status</strong></p>";
    }
}

// Afficher uniquement pour les administrateurs
if (current_user_can('administrator')) {
    echo '<div class="wrap">';
    echo '<h1>Vérification de sécurité Le Journal des Actus</h1>';
    echo '<p>Cette page affiche les résultats de la vérification de sécurité du thème Le Journal des Actus.</p>';
    
    LeJournalDesActus_Security_Check::run_all_checks();
    
    echo '</div>';
} else {
    wp_die('Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
}
