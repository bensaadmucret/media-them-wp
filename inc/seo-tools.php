<?php
/**
 * Fonctionnalités SEO pour Le Journal des Actus
 *
 * Ce fichier contient les fonctions liées au SEO du site.
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Personnaliser le contenu du robots.txt
 * 
 * Cette fonction remplace le robots.txt virtuel de WordPress
 * par une version personnalisée pour le thème.
 *
 * @param string $content Le contenu original du robots.txt
 * @return string Le contenu personnalisé du robots.txt
 */
function lejournaldesactus_custom_robots_txt($content) {
    $home_url = home_url();
    $sitemap_url = trailingslashit($home_url) . 'wp-sitemap.xml';
    
    $robots_content = "User-agent: *\n";
    $robots_content .= "Disallow: /wp-admin/\n";
    $robots_content .= "Allow: /wp-admin/admin-ajax.php\n\n";
    $robots_content .= "Sitemap: " . esc_url($sitemap_url) . "\n";
    
    return $robots_content;
}
add_filter('robots_txt', 'lejournaldesactus_custom_robots_txt', 10, 1);

/**
 * Ajouter des balises meta pour améliorer le SEO
 */
function lejournaldesactus_add_meta_tags() {
    // Ajouter la balise viewport si elle n'est pas déjà présente
    if (!wp_is_mobile()) {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
    }
    
    // Ajouter une balise canonical pour éviter le contenu dupliqué
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    }
}
add_action('wp_head', 'lejournaldesactus_add_meta_tags', 1);
