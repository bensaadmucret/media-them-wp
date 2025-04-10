<?php
/**
 * Fonctionnalités de sitemap XML
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour la gestion du sitemap
 */
class Lejournaldesactus_Sitemap {
    
    /**
     * Initialiser les hooks
     */
    public static function init() {
        // Ajouter le point de terminaison pour le sitemap
        add_action('init', array(__CLASS__, 'add_sitemap_rewrite_rules'));
        
        // Intercepter la requête de sitemap
        add_action('template_redirect', array(__CLASS__, 'render_sitemap'));
        
        // Ping les moteurs de recherche lors de la publication d'articles
        add_action('publish_post', array(__CLASS__, 'ping_search_engines'));
    }
    
    /**
     * Ajouter la règle de réécriture pour le sitemap
     */
    public static function add_sitemap_rewrite_rules() {
        add_rewrite_rule('^sitemap\.xml$', 'index.php?lejournaldesactus_sitemap=1', 'top');
        add_rewrite_tag('%lejournaldesactus_sitemap%', '([0-1]+)');
        
        // Vérifier si les règles sont déjà enregistrées
        $rules = get_option('rewrite_rules');
        if (!isset($rules['^sitemap\.xml$'])) {
            flush_rewrite_rules();
        }
    }
    
    /**
     * Générer le contenu du sitemap
     */
    public static function render_sitemap() {
        if (!get_query_var('lejournaldesactus_sitemap')) {
            return;
        }
        
        // Définir l'entête XML
        header('Content-Type: application/xml; charset=UTF-8');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Ajouter la page d'accueil
        echo '<url>' . "\n";
        echo '<loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        echo '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime(get_lastpostmodified('GMT'))) . '</lastmod>' . "\n";
        echo '<changefreq>daily</changefreq>' . "\n";
        echo '<priority>1.0</priority>' . "\n";
        echo '</url>' . "\n";
        
        // Ajouter les articles
        $posts = get_posts(array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ));
        
        foreach ($posts as $post) {
            echo '<url>' . "\n";
            echo '<loc>' . esc_url(get_permalink($post->ID)) . '</loc>' . "\n";
            echo '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($post->post_modified_gmt)) . '</lastmod>' . "\n";
            echo '<changefreq>weekly</changefreq>' . "\n";
            echo '<priority>0.8</priority>' . "\n";
            echo '</url>' . "\n";
        }
        
        // Ajouter les pages
        $pages = get_posts(array(
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ));
        
        foreach ($pages as $page) {
            echo '<url>' . "\n";
            echo '<loc>' . esc_url(get_permalink($page->ID)) . '</loc>' . "\n";
            echo '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($page->post_modified_gmt)) . '</lastmod>' . "\n";
            echo '<changefreq>monthly</changefreq>' . "\n";
            echo '<priority>0.6</priority>' . "\n";
            echo '</url>' . "\n";
        }
        
        // Ajouter les auteurs personnalisés
        $authors = get_posts(array(
            'post_type'      => 'author',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ));
        
        foreach ($authors as $author) {
            echo '<url>' . "\n";
            echo '<loc>' . esc_url(get_permalink($author->ID)) . '</loc>' . "\n";
            echo '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($author->post_modified_gmt)) . '</lastmod>' . "\n";
            echo '<changefreq>monthly</changefreq>' . "\n";
            echo '<priority>0.5</priority>' . "\n";
            echo '</url>' . "\n";
        }
        
        // Ajouter les catégories
        $categories = get_categories(array(
            'hide_empty' => true,
        ));
        
        foreach ($categories as $category) {
            echo '<url>' . "\n";
            echo '<loc>' . esc_url(get_category_link($category->term_id)) . '</loc>' . "\n";
            echo '<changefreq>weekly</changefreq>' . "\n";
            echo '<priority>0.5</priority>' . "\n";
            echo '</url>' . "\n";
        }
        
        // Ajouter les tags
        $tags = get_tags(array(
            'hide_empty' => true,
        ));
        
        foreach ($tags as $tag) {
            echo '<url>' . "\n";
            echo '<loc>' . esc_url(get_tag_link($tag->term_id)) . '</loc>' . "\n";
            echo '<changefreq>weekly</changefreq>' . "\n";
            echo '<priority>0.3</priority>' . "\n";
            echo '</url>' . "\n";
        }
        
        echo '</urlset>';
        
        exit;
    }
    
    /**
     * Notifier les moteurs de recherche de la mise à jour du sitemap
     */
    public static function ping_search_engines() {
        // Ne pas envoyer de ping si le site est en maintenance ou privé
        if (get_option('blog_public') != 1 || get_theme_mod('lejournaldesactus_maintenance_mode', false)) {
            return;
        }
        
        $sitemap_url = home_url('/sitemap.xml');
        
        // Ping Google
        wp_remote_get('https://www.google.com/ping?sitemap=' . urlencode($sitemap_url), array('blocking' => false));
        
        // Ping Bing
        wp_remote_get('https://www.bing.com/ping?sitemap=' . urlencode($sitemap_url), array('blocking' => false));
    }
}

// Initialiser le sitemap
Lejournaldesactus_Sitemap::init();
