<?php
/**
 * Fonctionnalité de temps de lecture estimé
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

// Toutes les options du temps de lecture sont désormais gérées via Kirki dans kirki-config.php
// Cette fonction et le hook associés ne sont plus nécessaires

/**
 * Fonction de validation pour les cases à cocher
 */
if (!function_exists('lejournaldesactus_sanitize_checkbox')) {
    function lejournaldesactus_sanitize_checkbox($input) {
        return (isset($input) && true == $input) ? true : false;
    }
}

/**
 * Fonction de validation pour les sélecteurs
 */
if (!function_exists('lejournaldesactus_sanitize_select')) {
    function lejournaldesactus_sanitize_select($input, $setting) {
        // Les options valides
        $choices = $setting->manager->get_control($setting->id)->choices;
        
        // Retourne l'entrée si elle est valide ou la valeur par défaut
        return (array_key_exists($input, $choices) ? $input : $setting->default);
    }
}

/**
 * Calculer le temps de lecture estimé
 */
function lejournaldesactus_get_reading_time($post_id = null, $format = 'minutes') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Récupérer le contenu de l'article
    $post = get_post($post_id);
    $content = $post->post_content;
    
    // Récupérer les options
    $wpm = get_theme_mod('lejournaldesactus_reading_time_wpm', 200);
    $include_images = get_theme_mod('lejournaldesactus_reading_time_include_images', true);
    $image_time = get_theme_mod('lejournaldesactus_reading_time_image_time', 12);
    
    // Compter les images avant de supprimer le HTML
    $image_count = 0;
    if ($include_images) {
        $image_count = substr_count($content, '<img');
        if (has_post_thumbnail($post_id)) {
            $image_count++;
        }
    }
    $content = wp_strip_all_tags($content);
    $word_count = str_word_count($content);
    $reading_time = $word_count / $wpm;
    if ($include_images && $image_count > 0) {
        $reading_time += ($image_count * $image_time) / 60;
    }

    $reading_time = max(1, round($reading_time));

    if ($format === 'html') {
        return lejournaldesactus_get_reading_time_html($reading_time);
    } elseif ($format === 'minutes') {
        return $reading_time;
    } else {
        $minutes = floor($reading_time);
        $seconds = round(($reading_time - $minutes) * 60);
        return array(
            'minutes' => $minutes,
            'seconds' => $seconds,
        );
    }
}

function lejournaldesactus_get_reading_time_html($reading_time) {
    $prefix = get_theme_mod('lejournaldesactus_reading_time_prefix', __('Temps de lecture :', 'lejournaldesactus'));
    $suffix = get_theme_mod('lejournaldesactus_reading_time_suffix', __('min de lecture', 'lejournaldesactus'));
    $style = get_theme_mod('lejournaldesactus_reading_time_style', 'text');
    $color = get_theme_mod('lejournaldesactus_reading_time_color', '#007bff');
    
    $output = '';
    
    switch ($style) {
        case 'icon':
            $output = '<span class="reading-time reading-time-icon">';
            $output .= '<i class="bi bi-clock"></i> ';
            
            if (!empty($prefix)) {
                $output .= '<span class="reading-time-prefix">' . esc_html($prefix) . '</span> ';
            }
            
            $output .= '<span class="reading-time-value">' . esc_html($reading_time) . '</span>';
            
            if (!empty($suffix)) {
                $output .= ' <span class="reading-time-suffix">' . esc_html($suffix) . '</span>';
            }
            
            $output .= '</span>';
            break;
            
        case 'badge':
            $output = '<span class="reading-time reading-time-badge">';
            $output .= '<i class="bi bi-clock"></i> ';
            $output .= '<span class="reading-time-value">' . esc_html($reading_time) . '</span>';
            
            if (!empty($suffix)) {
                $output .= ' <span class="reading-time-suffix">' . esc_html($suffix) . '</span>';
            }
            
            $output .= '</span>';
            break;
            
        case 'text':
        default:
            $output = '<span class="reading-time reading-time-text">';
            if (!empty($prefix)) {
                $output .= '<span class="reading-time-prefix">' . esc_html($prefix) . '</span> ';
            }
            
            $output .= '<span class="reading-time-value">' . esc_html($reading_time) . '</span>';
            
            if (!empty($suffix)) {
                $output .= ' <span class="reading-time-suffix">' . esc_html($suffix) . '</span>';
            }
            
            $output .= '</span>';
            break;
    }
    
    return $output;
}

/**
 * Afficher le temps de lecture
 */
function lejournaldesactus_display_reading_time() {
    // Vérifier si l'affichage du temps de lecture est activé
    if (!get_theme_mod('lejournaldesactus_show_reading_time', true)) {
        return;
    }
    
    // Ne pas afficher sur les pages
    if (!is_singular('post')) {
        return;
    }
    
    // Récupérer le temps de lecture
    $reading_time_html = lejournaldesactus_get_reading_time(null, 'html');
    
    echo $reading_time_html;
}

/**
 * Ajouter le temps de lecture à l'emplacement choisi
 */
function lejournaldesactus_add_reading_time() {
    // Vérifier si l'affichage du temps de lecture est activé
    if (!get_theme_mod('lejournaldesactus_show_reading_time', true)) {
        return;
    }
    
    $display_location = get_theme_mod('lejournaldesactus_reading_time_display', 'before_content');
    
    switch ($display_location) {
        case 'before_title':
            add_action('lejournaldesactus_before_post_title', 'lejournaldesactus_display_reading_time');
            break;
            
        case 'after_title':
            add_action('lejournaldesactus_after_post_title', 'lejournaldesactus_display_reading_time');
            break;
            
        case 'with_meta':
            add_action('lejournaldesactus_post_meta', 'lejournaldesactus_display_reading_time');
            break;
            
        case 'before_content':
        default:
            add_action('lejournaldesactus_before_post_content', 'lejournaldesactus_display_reading_time');
            break;
    }
}
add_action('wp', 'lejournaldesactus_add_reading_time');

/**
 * Ajouter les styles CSS pour le temps de lecture
 */
function lejournaldesactus_reading_time_styles() {
    // Vérifier si l'affichage du temps de lecture est activé
    if (!get_theme_mod('lejournaldesactus_show_reading_time', true)) {
        return;
    }
    
    $color = get_theme_mod('lejournaldesactus_reading_time_color', '#007bff');
    
    ?>
    <style>
        .reading-time {
            display: inline-block;
            margin: 10px 0;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .reading-time-text {
            color: #6c757d;
        }
        
        .reading-time-icon {
            color: <?php echo esc_attr($color); ?>;
        }
        
        .reading-time-icon i {
            margin-right: 5px;
        }
        
        .reading-time-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            background-color: <?php echo esc_attr($color); ?>;
            color: #fff;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .reading-time-badge i {
            margin-right: 5px;
        }
        
        /* Styles pour l'affichage avec les métadonnées */
        .post-meta .reading-time {
            margin: 0 10px 0 0;
        }
    </style>
    <?php
}
add_action('wp_head', 'lejournaldesactus_reading_time_styles');

/**
 * Ajouter le temps de lecture dans les métadonnées de l'article
 */
function lejournaldesactus_add_reading_time_to_meta($meta_html) {
    // Vérifier si l'affichage du temps de lecture est activé
    if (!get_theme_mod('lejournaldesactus_show_reading_time', true)) {
        return $meta_html;
    }
    
    // Vérifier si l'emplacement d'affichage est avec les métadonnées
    if (get_theme_mod('lejournaldesactus_reading_time_display', 'before_content') !== 'with_meta') {
        return $meta_html;
    }
    
    // Récupérer le temps de lecture
    $reading_time_html = lejournaldesactus_get_reading_time(null, 'html');
    
    // Ajouter le temps de lecture aux métadonnées
    $meta_html .= ' ' . $reading_time_html;
    
    return $meta_html;
}
// add_filter('lejournaldesactus_post_meta_html', 'lejournaldesactus_add_reading_time_to_meta');

/**
 * Ajouter les hooks manquants si nécessaire
 */
function lejournaldesactus_add_reading_time_hooks() {
    // Vérifier si le thème utilise déjà les hooks nécessaires
    $single_template = get_template_directory() . '/single.php';
    
    if (file_exists($single_template)) {
        $single_content = file_get_contents($single_template);
        
        // Si les hooks ne sont pas déjà présents, les ajouter via JavaScript
        if (strpos($single_content, 'lejournaldesactus_before_post_title') === false &&
            strpos($single_content, 'lejournaldesactus_after_post_title') === false &&
            strpos($single_content, 'lejournaldesactus_before_post_content') === false) {
            
            add_action('wp_footer', 'lejournaldesactus_add_reading_time_js');
        }
    }
}
// add_action('wp', 'lejournaldesactus_add_reading_time_hooks');

/**
 * Ajouter le temps de lecture via JavaScript si les hooks ne sont pas disponibles
 */
function lejournaldesactus_add_reading_time_js() {
    // Vérifier si l'affichage du temps de lecture est activé
    if (!get_theme_mod('lejournaldesactus_show_reading_time', true) || !is_singular('post')) {
        return;
    }
    
    $display_location = get_theme_mod('lejournaldesactus_reading_time_display', 'before_content');
    $reading_time_html = lejournaldesactus_get_reading_time(null, 'html');
    
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const readingTimeHtml = <?php echo json_encode($reading_time_html); ?>;
            const readingTimeElement = document.createElement('div');
            readingTimeElement.innerHTML = readingTimeHtml;
            
            <?php if ($display_location === 'before_title') : ?>
            const postTitle = document.querySelector('.entry-title, .post-title, article h1');
            if (postTitle) {
                postTitle.parentNode.insertBefore(readingTimeElement, postTitle);
            }
            <?php elseif ($display_location === 'after_title') : ?>
            const postTitle = document.querySelector('.entry-title, .post-title, article h1');
            if (postTitle) {
                postTitle.parentNode.insertBefore(readingTimeElement, postTitle.nextSibling);
            }
            <?php elseif ($display_location === 'with_meta') : ?>
            const postMeta = document.querySelector('.entry-meta, .post-meta');
            if (postMeta) {
                postMeta.appendChild(readingTimeElement);
            }
            <?php else : // before_content ?>
            const postContent = document.querySelector('.entry-content, .post-content, article .content');
            if (postContent) {
                postContent.parentNode.insertBefore(readingTimeElement, postContent);
            }
            <?php endif; ?>
        });
    </script>
    <?php
}

/**
 * Ajouter le temps de lecture aux articles dans les résultats de recherche et les archives
 */
function lejournaldesactus_add_reading_time_to_excerpt($excerpt) {
    // Vérifier si l'affichage du temps de lecture est activé
    if (!get_theme_mod('lejournaldesactus_show_reading_time', true)) {
        return $excerpt;
    }
    
    // Ne pas ajouter sur les pages singulières
    if (is_singular()) {
        return $excerpt;
    }
    
    // Récupérer le temps de lecture
    $reading_time_html = lejournaldesactus_get_reading_time(get_the_ID(), 'html');
    
    // Ajouter le temps de lecture à l'extrait
    $excerpt = $reading_time_html . ' ' . $excerpt;
    
    return $excerpt;
}
// add_filter('the_excerpt', 'lejournaldesactus_add_reading_time_to_excerpt');

/**
 * Ajouter une colonne de temps de lecture dans l'administration
 */
function lejournaldesactus_add_reading_time_column($columns) {
    $columns['reading_time'] = __('Temps de lecture', 'lejournaldesactus');
    return $columns;
}
// add_filter('manage_posts_columns', 'lejournaldesactus_add_reading_time_column');

/**
 * Afficher le temps de lecture dans la colonne d'administration
 */
function lejournaldesactus_show_reading_time_column($column, $post_id) {
    if ($column === 'reading_time') {
        $reading_time = lejournaldesactus_get_reading_time($post_id, 'minutes');
        echo esc_html($reading_time) . ' ' . __('min', 'lejournaldesactus');
    }
}
// add_action('manage_posts_custom_column', 'lejournaldesactus_show_reading_time_column', 10, 2);
