<?php
/**
 * Widget Pages Sélectionnées
 * Permet de sélectionner spécifiquement les pages à afficher
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour le widget de pages sélectionnées
 */
class Lejournaldesactus_Selected_Pages_Widget extends WP_Widget {

    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_selected_pages',
            __('Pages Sélectionnées', 'lejournaldesactus'),
            array(
                'description' => __('Affiche une liste de pages que vous sélectionnez spécifiquement.', 'lejournaldesactus'),
                'classname'   => 'widget-selected-pages',
            )
        );
    }

    /**
     * Affichage du widget dans le frontend
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        // Titre du widget
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Récupérer les pages sélectionnées
        $selected_pages = isset($instance['selected_pages']) ? $instance['selected_pages'] : array();
        
        if (!empty($selected_pages)) {
            // Options d'affichage
            $show_description = isset($instance['show_description']) ? $instance['show_description'] : false;
            $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;
            $display_style = isset($instance['display_style']) ? $instance['display_style'] : 'list';
            
            // Afficher les pages selon le style choisi
            if ($display_style === 'grid') {
                echo '<div class="pages-grid">';
            } else {
                echo '<ul class="pages-list">';
            }
            
            foreach ($selected_pages as $page_id) {
                $page = get_post($page_id);
                
                if ($page) {
                    if ($display_style === 'grid') {
                        echo '<div class="page-item">';
                    } else {
                        echo '<li class="page-item">';
                    }
                    
                    // Afficher le titre avec le lien
                    echo '<a href="' . esc_url(get_permalink($page->ID)) . '" class="page-title">' . esc_html($page->post_title) . '</a>';
                    
                    // Afficher la description (extrait) si demandé
                    if ($show_description) {
                        $excerpt = has_excerpt($page->ID) ? $page->post_excerpt : wp_trim_words($page->post_content, 20);
                        echo '<div class="page-excerpt">' . esc_html($excerpt) . '</div>';
                    }
                    
                    // Afficher la date si demandé
                    if ($show_date) {
                        echo '<div class="page-date">' . esc_html(get_the_date('', $page->ID)) . '</div>';
                    }
                    
                    if ($display_style === 'grid') {
                        echo '</div>';
                    } else {
                        echo '</li>';
                    }
                }
            }
            
            if ($display_style === 'grid') {
                echo '</div>';
            } else {
                echo '</ul>';
            }
        } else {
            echo '<p>' . __('Aucune page sélectionnée.', 'lejournaldesactus') . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Pages importantes', 'lejournaldesactus');
        $selected_pages = isset($instance['selected_pages']) ? $instance['selected_pages'] : array();
        $show_description = isset($instance['show_description']) ? $instance['show_description'] : false;
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;
        $display_style = isset($instance['display_style']) ? $instance['display_style'] : 'list';
        
        // Récupérer toutes les pages publiées
        $pages = get_pages(array(
            'sort_column' => 'post_title',
            'sort_order'  => 'ASC',
            'post_status' => 'publish'
        ));
        ?>
        
        <!-- Titre -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Titre:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <!-- Pages sélectionnées -->
        <p>
            <label><?php _e('Sélectionner les pages à afficher:', 'lejournaldesactus'); ?></label>
            <div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 5px; margin-top: 5px;">
                <?php foreach ($pages as $page) : ?>
                    <p>
                        <input type="checkbox" 
                               id="<?php echo esc_attr($this->get_field_id('selected_pages') . '-' . $page->ID); ?>" 
                               name="<?php echo esc_attr($this->get_field_name('selected_pages')); ?>[]" 
                               value="<?php echo esc_attr($page->ID); ?>" 
                               <?php checked(in_array($page->ID, $selected_pages)); ?>>
                        <label for="<?php echo esc_attr($this->get_field_id('selected_pages') . '-' . $page->ID); ?>">
                            <?php echo esc_html($page->post_title); ?>
                        </label>
                    </p>
                <?php endforeach; ?>
            </div>
        </p>
        
        <!-- Options d'affichage -->
        <p>
            <input type="checkbox" 
                   id="<?php echo esc_attr($this->get_field_id('show_description')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_description')); ?>" 
                   <?php checked($show_description); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_description')); ?>">
                <?php _e('Afficher la description', 'lejournaldesactus'); ?>
            </label>
        </p>
        
        <p>
            <input type="checkbox" 
                   id="<?php echo esc_attr($this->get_field_id('show_date')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" 
                   <?php checked($show_date); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>">
                <?php _e('Afficher la date', 'lejournaldesactus'); ?>
            </label>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('display_style')); ?>"><?php _e('Style d\'affichage:', 'lejournaldesactus'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('display_style')); ?>" name="<?php echo esc_attr($this->get_field_name('display_style')); ?>" class="widefat">
                <option value="list" <?php selected($display_style, 'list'); ?>><?php _e('Liste', 'lejournaldesactus'); ?></option>
                <option value="grid" <?php selected($display_style, 'grid'); ?>><?php _e('Grille', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        
        <?php
    }

    /**
     * Sauvegarde des options du widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['selected_pages'] = (!empty($new_instance['selected_pages'])) ? array_map('intval', $new_instance['selected_pages']) : array();
        $instance['show_description'] = isset($new_instance['show_description']) ? (bool) $new_instance['show_description'] : false;
        $instance['show_date'] = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        $instance['display_style'] = (!empty($new_instance['display_style'])) ? sanitize_text_field($new_instance['display_style']) : 'list';
        
        return $instance;
    }
}

/**
 * Enregistrer le widget
 */
function lejournaldesactus_register_selected_pages_widget() {
    register_widget('Lejournaldesactus_Selected_Pages_Widget');
}
add_action('widgets_init', 'lejournaldesactus_register_selected_pages_widget');

/**
 * Ajouter le style pour le widget
 */
function lejournaldesactus_selected_pages_widget_styles() {
    ?>
    <style>
        /* Styles pour le widget de pages sélectionnées */
        .widget-selected-pages .pages-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .widget-selected-pages .page-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .widget-selected-pages .page-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .widget-selected-pages .page-title {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
            text-decoration: none;
        }
        
        .widget-selected-pages .page-title:hover {
            text-decoration: underline;
        }
        
        .widget-selected-pages .page-excerpt {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        
        .widget-selected-pages .page-date {
            font-size: 0.8em;
            color: #999;
            font-style: italic;
        }
        
        /* Styles pour la grille */
        .widget-selected-pages .pages-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .widget-selected-pages .pages-grid .page-item {
            border: 1px solid rgba(0, 0, 0, 0.1);
            padding: 10px;
            border-radius: 4px;
        }
        
        @media (max-width: 768px) {
            .widget-selected-pages .pages-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'lejournaldesactus_selected_pages_widget_styles');
