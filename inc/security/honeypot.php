<?php
/**
 * Sécurité : Honeypot & Anti-flood anti-spam commentaires
 */
if (!defined('ABSPATH')) exit;

// Ajoute les champs honeypot et timestamp au formulaire de commentaire
add_filter('comment_form_defaults', function($defaults) {
    $defaults['fields']['lejournaldesactus_hp'] = '<p style="display:none !important;"><label>Ne pas remplir : <input type="text" name="lejournaldesactus_hp" value="" autocomplete="off"></label></p>';
    $defaults['fields']['lejournaldesactus_ts'] = '<input type="hidden" name="lejournaldesactus_ts" value="' . time() . '">';
    return $defaults;
});

// Bloque les soumissions suspectes (honeypot rempli ou anti-flood)
add_filter('preprocess_comment', function($commentdata) {
    // Honeypot : si le champ caché est rempli, on bloque
    if (!empty($_POST['lejournaldesactus_hp'])) {
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url();
        wp_safe_redirect(add_query_arg('comment_error', 'honeypot', $ref));
        exit;
    }
    // Anti-flood : délai minimal (5s) entre affichage et soumission
    if (isset($_POST['lejournaldesactus_ts'])) {
        $min_delay = 5; // secondes
        $now = time();
        $elapsed = $now - intval($_POST['lejournaldesactus_ts']);
        if ($elapsed < $min_delay) {
            $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url();
            wp_safe_redirect(add_query_arg('comment_error', 'flood', $ref));
            exit;
        }
    }
    return $commentdata;
}, 20);

// Ajoute un captcha mathématique simple (optionnel)
add_filter('comment_form_defaults', function($defaults) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $a = rand(1, 9);
        $b = rand(1, 9);
        $question = "$a + $b = ?";
        $expected = $a + $b;
        $defaults['fields']['lejournaldesactus_captcha'] = '<p class="lejournaldesactus-captcha-field"><label for="lejournaldesactus_captcha">Question anti-spam : <span class="lejournaldesactus-captcha-question" style="font-weight:bold">' . $question . '</span> <input type="text" name="lejournaldesactus_captcha" id="lejournaldesactus_captcha" size="2" maxlength="2" required autocomplete="off">' . '<input type="hidden" name="lejournaldesactus_captcha_expected" value="' . $expected . '"></label></p>';
    }
    return $defaults;
});

add_filter('preprocess_comment', function($commentdata) {
    $expected = isset($_POST['lejournaldesactus_captcha_expected']) ? intval($_POST['lejournaldesactus_captcha_expected']) : null;
    $provided = isset($_POST['lejournaldesactus_captcha']) ? intval($_POST['lejournaldesactus_captcha']) : null;
    if ($expected === null || $provided !== $expected) {
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url();
        wp_safe_redirect(add_query_arg('comment_error', 'captcha', $ref));
        exit;
    }
    return $commentdata;
});
