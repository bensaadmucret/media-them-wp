<?php
/**
 * Gestion des liens vers les réseaux sociaux
 * 
 * Ce fichier contient les fonctions pour gérer les liens vers les réseaux sociaux
 * dans le thème Le Journal des Actus.
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Ajoute les options de réseaux sociaux au Customizer
 * 
 * @param WP_Customize_Manager $wp_customize Objet Customizer
 */
function lejournaldesactus_social_links_customizer($wp_customize) {
    // Ajouter une section pour les réseaux sociaux
    $wp_customize->add_section('lejournaldesactus_social_links_section', array(
        'title'       => __('Réseaux Sociaux', 'lejournaldesactus'),
        'description' => __('Configurez les liens vers vos réseaux sociaux.', 'lejournaldesactus'),
        'priority'    => 120,
    ));
    
    // Tableau des réseaux sociaux disponibles
    $social_networks = array(
        'facebook'  => __('Facebook', 'lejournaldesactus'),
        'twitter'   => __('Twitter', 'lejournaldesactus'),
        'instagram' => __('Instagram', 'lejournaldesactus'),
        'linkedin'  => __('LinkedIn', 'lejournaldesactus'),
        'youtube'   => __('YouTube', 'lejournaldesactus'),
        'pinterest' => __('Pinterest', 'lejournaldesactus'),
        'github'    => __('GitHub', 'lejournaldesactus'),
    );
    
    // Ajouter les champs pour chaque réseau social
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('lejournaldesactus_social_' . $network, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control('lejournaldesactus_social_' . $network, array(
            'label'    => $label,
            'section'  => 'lejournaldesactus_social_links_section',
            'type'     => 'url',
            'input_attrs' => array(
                'placeholder' => 'https://',
            ),
        ));
    }
    
    // Option pour activer/désactiver les réseaux sociaux dans le header
    $wp_customize->add_setting('lejournaldesactus_show_social_header', array(
        'default'           => true,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_show_social_header', array(
        'label'    => __('Afficher dans le header', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_social_links_section',
        'type'     => 'checkbox',
    ));
    
    // Option pour activer/désactiver les réseaux sociaux dans le footer
    $wp_customize->add_setting('lejournaldesactus_show_social_footer', array(
        'default'           => true,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_show_social_footer', array(
        'label'    => __('Afficher dans le footer', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_social_links_section',
        'type'     => 'checkbox',
    ));
    
    // Option pour activer/désactiver les réseaux sociaux dans les articles
    $wp_customize->add_setting('lejournaldesactus_show_social_articles', array(
        'default'           => true,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_show_social_articles', array(
        'label'    => __('Afficher dans les articles', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_social_links_section',
        'type'     => 'checkbox',
    ));
}
add_action('customize_register', 'lejournaldesactus_social_links_customizer');

/**
 * Récupère les liens vers les réseaux sociaux
 * 
 * @return array Tableau des liens vers les réseaux sociaux
 */
function lejournaldesactus_get_social_links() {
    $social_networks = array(
        'facebook'  => array(
            'url'   => get_theme_mod('lejournaldesactus_social_facebook', ''),
            'icon'  => 'bi bi-facebook',
            'label' => __('Facebook', 'lejournaldesactus'),
        ),
        'twitter'   => array(
            'url'   => get_theme_mod('lejournaldesactus_social_twitter', ''),
            'icon'  => 'bi bi-twitter',
            'label' => __('Twitter', 'lejournaldesactus'),
        ),
        'instagram' => array(
            'url'   => get_theme_mod('lejournaldesactus_social_instagram', ''),
            'icon'  => 'bi bi-instagram',
            'label' => __('Instagram', 'lejournaldesactus'),
        ),
        'linkedin'  => array(
            'url'   => get_theme_mod('lejournaldesactus_social_linkedin', ''),
            'icon'  => 'bi bi-linkedin',
            'label' => __('LinkedIn', 'lejournaldesactus'),
        ),
        'youtube'   => array(
            'url'   => get_theme_mod('lejournaldesactus_social_youtube', ''),
            'icon'  => 'bi bi-youtube',
            'label' => __('YouTube', 'lejournaldesactus'),
        ),
        'pinterest' => array(
            'url'   => get_theme_mod('lejournaldesactus_social_pinterest', ''),
            'icon'  => 'bi bi-pinterest',
            'label' => __('Pinterest', 'lejournaldesactus'),
        ),
        'github'    => array(
            'url'   => get_theme_mod('lejournaldesactus_social_github', ''),
            'icon'  => 'bi bi-github',
            'label' => __('GitHub', 'lejournaldesactus'),
        ),
    );
    
    // Filtrer les réseaux sociaux sans URL
    return array_filter($social_networks, function($network) {
        return !empty($network['url']);
    });
}

/**
 * Affiche les liens vers les réseaux sociaux
 * 
 * @param string $location Emplacement des liens (header, footer, article)
 */
function lejournaldesactus_display_social_links($location = 'header') {
    // Vérifier si l'affichage est activé pour cet emplacement
    $show_social = get_theme_mod('lejournaldesactus_show_social_' . $location, true);
    
    if (!$show_social) {
        return;
    }
    
    $social_links = lejournaldesactus_get_social_links();
    
    if (empty($social_links)) {
        return;
    }
    
    // Ajouter une classe CSS spécifique à l'emplacement
    $class = 'social-links';
    if ($location === 'header') {
        $class .= ' d-none d-lg-block';
    } elseif ($location === 'footer') {
        $class .= ' d-flex mt-4';
    }
    
    echo '<div class="' . esc_attr($class) . '">';
    
    // Si nous sommes dans un article, utiliser les liens de partage
    if ($location === 'articles' && is_single()) {
        $permalink = get_permalink();
        $title = get_the_title();
        
        // Liens de partage pour les articles
        $share_links = array(
            'facebook' => array(
                'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($permalink),
                'icon' => 'bi bi-facebook',
                'label' => __('Partager sur Facebook', 'lejournaldesactus'),
            ),
            'twitter' => array(
                'url' => 'https://twitter.com/intent/tweet?url=' . urlencode($permalink) . '&text=' . urlencode($title),
                'icon' => 'bi bi-twitter',
                'label' => __('Partager sur Twitter', 'lejournaldesactus'),
            ),
            'linkedin' => array(
                'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($permalink),
                'icon' => 'bi bi-linkedin',
                'label' => __('Partager sur LinkedIn', 'lejournaldesactus'),
            ),
            'pinterest' => array(
                'url' => 'https://pinterest.com/pin/create/button/?url=' . urlencode($permalink) . '&description=' . urlencode($title),
                'icon' => 'bi bi-pinterest',
                'label' => __('Partager sur Pinterest', 'lejournaldesactus'),
            ),
            'email' => array(
                'url' => 'mailto:?subject=' . urlencode($title) . '&body=' . urlencode($permalink),
                'icon' => 'bi bi-envelope',
                'label' => __('Partager par email', 'lejournaldesactus'),
            ),
        );
        
        foreach ($share_links as $network => $data) {
            printf(
                '<a href="%s" class="%s" aria-label="%s" rel="noopener" target="_blank"><i class="%s"></i></a>',
                esc_url($data['url']),
                esc_attr($network),
                esc_attr($data['label']),
                esc_attr($data['icon'])
            );
        }
    } else {
        // Liens normaux vers les profils de réseaux sociaux
        foreach ($social_links as $network => $data) {
            printf(
                '<a href="%s" class="%s" aria-label="%s" rel="noopener" target="_blank"><i class="%s"></i></a>',
                esc_url($data['url']),
                esc_attr($network),
                esc_attr($data['label']),
                esc_attr($data['icon'])
            );
        }
    }
    
    echo '</div>';
}
