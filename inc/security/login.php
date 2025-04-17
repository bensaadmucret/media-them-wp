<?php
/**
 * Sécurité : Connexion et restrictions
 */
if (!defined('ABSPATH')) exit;

// Extrait de la logique de sécurité de connexion depuis security.php
// Ex : function lejournaldesactus_login_security() { ... }

// Limiter le nombre de tentatives de connexion (brute force)
add_action('wp_login_failed', function($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'lejournaldesactus_login_attempts_' . md5($ip);
    $attempts = (int) get_transient($key);
    $attempts++;
    set_transient($key, $attempts, 30 * MINUTE_IN_SECONDS);
    if ($attempts >= 5) {
        set_transient('lejournaldesactus_login_blocked_' . md5($ip), true, 15 * MINUTE_IN_SECONDS);
    }
});

add_filter('authenticate', function($user, $username, $password) {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (get_transient('lejournaldesactus_login_blocked_' . md5($ip))) {
        return new WP_Error('too_many_attempts', __('Trop de tentatives de connexion. Réessayez dans 15 minutes.', 'lejournaldesactus'));
    }
    return $user;
}, 30, 3);

// Réinitialiser le compteur en cas de succès
add_action('wp_login', function($user_login, $user) {
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient('lejournaldesactus_login_attempts_' . md5($ip));
    delete_transient('lejournaldesactus_login_blocked_' . md5($ip));
}, 10, 2);

// Masquer les erreurs détaillées de connexion
add_filter('login_errors', function($error) {
    return __('Identifiants incorrects.', 'lejournaldesactus');
});
