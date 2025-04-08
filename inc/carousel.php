<?php
/**
 * Gestion des carrousels
 * 
 * Ce fichier contient les fonctions pour créer et gérer des carrousels
 * dans le thème Le Journal des Actus.
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour la gestion des carrousels
 */
class Lejournaldesactus_Carousel {
    /**
     * Initialisation de la classe
     */
    public function __construct() {
        add_action('init', array($this, 'register_carousel_post_type'));
        add_action('add_meta_boxes', array($this, 'add_carousel_meta_boxes'));
        add_action('save_post', array($this, 'save_carousel_meta'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_shortcode('lejournaldesactus_carousel', array($this, 'carousel_shortcode'));
    }

    /**
     * Enregistrer le type de contenu pour les carrousels
     */
    public function register_carousel_post_type() {
        $labels = array(
            'name'               => _x('Carrousels', 'post type general name', 'lejournaldesactus'),
            'singular_name'      => _x('Carrousel', 'post type singular name', 'lejournaldesactus'),
            'menu_name'          => _x('Carrousels', 'admin menu', 'lejournaldesactus'),
            'name_admin_bar'     => _x('Carrousel', 'add new on admin bar', 'lejournaldesactus'),
            'add_new'            => _x('Ajouter', 'carousel', 'lejournaldesactus'),
            'add_new_item'       => __('Ajouter un nouveau carrousel', 'lejournaldesactus'),
            'new_item'           => __('Nouveau carrousel', 'lejournaldesactus'),
            'edit_item'          => __('Modifier le carrousel', 'lejournaldesactus'),
            'view_item'          => __('Voir le carrousel', 'lejournaldesactus'),
            'all_items'          => __('Tous les carrousels', 'lejournaldesactus'),
            'search_items'       => __('Rechercher des carrousels', 'lejournaldesactus'),
            'parent_item_colon'  => __('Carrousels parents:', 'lejournaldesactus'),
            'not_found'          => __('Aucun carrousel trouvé.', 'lejournaldesactus'),
            'not_found_in_trash' => __('Aucun carrousel trouvé dans la corbeille.', 'lejournaldesactus')
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __('Carrousels pour le thème Le Journal des Actus', 'lejournaldesactus'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'carousel'),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-slides',
            'supports'           => array('title', 'thumbnail')
        );

        register_post_type('lejournaldesactus_carousel', $args);
    }

    /**
     * Ajouter les metaboxes pour les carrousels
     */
    public function add_carousel_meta_boxes() {
        add_meta_box(
            'lejournaldesactus_carousel_slides',
            __('Slides du carrousel', 'lejournaldesactus'),
            array($this, 'carousel_slides_callback'),
            'lejournaldesactus_carousel',
            'normal',
            'high'
        );

        add_meta_box(
            'lejournaldesactus_carousel_settings',
            __('Paramètres du carrousel', 'lejournaldesactus'),
            array($this, 'carousel_settings_callback'),
            'lejournaldesactus_carousel',
            'side',
            'default'
        );
    }

    /**
     * Callback pour la metabox des slides
     */
    public function carousel_slides_callback($post) {
        wp_nonce_field('lejournaldesactus_carousel_meta', 'lejournaldesactus_carousel_meta_nonce');

        // Récupérer les slides existantes
        $slides = get_post_meta($post->ID, '_lejournaldesactus_carousel_slides', true);
        if (empty($slides) || !is_array($slides)) {
            $slides = array();
        }
        ?>
        <div id="lejournaldesactus-carousel-slides">
            <p>
                <button type="button" class="button button-primary" id="add-slide"><?php _e('Ajouter une slide', 'lejournaldesactus'); ?></button>
            </p>
            
            <div class="slides-container">
                <?php 
                if (!empty($slides)) {
                    foreach ($slides as $index => $slide) {
                        $this->render_slide_html($index, $slide);
                    }
                }
                ?>
            </div>
            
            <!-- Template pour les nouvelles slides -->
            <script type="text/html" id="tmpl-carousel-slide">
                <?php $this->render_slide_html('{{data.index}}', array('image_id' => '', 'title' => '', 'description' => '', 'button_text' => '', 'button_url' => '')); ?>
            </script>
        </div>
        <?php
    }

    /**
     * Rendre le HTML pour une slide
     */
    private function render_slide_html($index, $slide) {
        $image_url = '';
        if (!empty($slide['image_id'])) {
            $image_url = wp_get_attachment_image_url($slide['image_id'], 'large');
        }
        ?>
        <div class="carousel-slide" data-index="<?php echo esc_attr($index); ?>">
            <h3 class="slide-header">
                <?php _e('Slide', 'lejournaldesactus'); ?> #<span class="slide-number"><?php echo esc_html($index + 1); ?></span>
                <button type="button" class="button remove-slide"><?php _e('Supprimer', 'lejournaldesactus'); ?></button>
                <button type="button" class="button-link slide-toggle"><?php _e('Réduire/Développer', 'lejournaldesactus'); ?></button>
            </h3>
            
            <div class="slide-content">
                <div class="slide-image">
                    <div class="image-preview-wrapper">
                        <?php if ($image_url) : ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width:100%;">
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="carousel_slides[<?php echo esc_attr($index); ?>][image_id]" value="<?php echo esc_attr($slide['image_id']); ?>" class="slide-image-id">
                    <button type="button" class="button upload-image"><?php _e('Choisir une image', 'lejournaldesactus'); ?></button>
                    <?php if ($image_url) : ?>
                        <button type="button" class="button remove-image"><?php _e('Supprimer l\'image', 'lejournaldesactus'); ?></button>
                    <?php endif; ?>
                </div>
                
                <div class="slide-fields">
                    <p>
                        <label for="slide-title-<?php echo esc_attr($index); ?>"><?php _e('Titre:', 'lejournaldesactus'); ?></label>
                        <input type="text" id="slide-title-<?php echo esc_attr($index); ?>" name="carousel_slides[<?php echo esc_attr($index); ?>][title]" value="<?php echo esc_attr($slide['title']); ?>" class="widefat">
                    </p>
                    
                    <p>
                        <label for="slide-desc-<?php echo esc_attr($index); ?>"><?php _e('Description:', 'lejournaldesactus'); ?></label>
                        <textarea id="slide-desc-<?php echo esc_attr($index); ?>" name="carousel_slides[<?php echo esc_attr($index); ?>][description]" class="widefat" rows="3"><?php echo esc_textarea($slide['description']); ?></textarea>
                    </p>
                    
                    <p>
                        <label for="slide-btn-text-<?php echo esc_attr($index); ?>"><?php _e('Texte du bouton:', 'lejournaldesactus'); ?></label>
                        <input type="text" id="slide-btn-text-<?php echo esc_attr($index); ?>" name="carousel_slides[<?php echo esc_attr($index); ?>][button_text]" value="<?php echo esc_attr($slide['button_text']); ?>" class="widefat">
                    </p>
                    
                    <p>
                        <label for="slide-btn-url-<?php echo esc_attr($index); ?>"><?php _e('URL du bouton:', 'lejournaldesactus'); ?></label>
                        <input type="url" id="slide-btn-url-<?php echo esc_attr($index); ?>" name="carousel_slides[<?php echo esc_attr($index); ?>][button_url]" value="<?php echo esc_url($slide['button_url']); ?>" class="widefat">
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Callback pour la metabox des paramètres
     */
    public function carousel_settings_callback($post) {
        // Récupérer les paramètres existants
        $settings = get_post_meta($post->ID, '_lejournaldesactus_carousel_settings', true);
        if (empty($settings) || !is_array($settings)) {
            $settings = array(
                'autoplay' => 'yes',
                'loop' => 'yes',
                'speed' => 3000,
                'effect' => 'slide',
                'pagination' => 'yes',
                'navigation' => 'yes',
                'height' => 'default'
            );
        }
        ?>
        <p>
            <label for="carousel-autoplay"><?php _e('Lecture automatique:', 'lejournaldesactus'); ?></label>
            <select id="carousel-autoplay" name="carousel_settings[autoplay]" class="widefat">
                <option value="yes" <?php selected($settings['autoplay'], 'yes'); ?>><?php _e('Oui', 'lejournaldesactus'); ?></option>
                <option value="no" <?php selected($settings['autoplay'], 'no'); ?>><?php _e('Non', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="carousel-loop"><?php _e('Boucle:', 'lejournaldesactus'); ?></label>
            <select id="carousel-loop" name="carousel_settings[loop]" class="widefat">
                <option value="yes" <?php selected($settings['loop'], 'yes'); ?>><?php _e('Oui', 'lejournaldesactus'); ?></option>
                <option value="no" <?php selected($settings['loop'], 'no'); ?>><?php _e('Non', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="carousel-speed"><?php _e('Vitesse (ms):', 'lejournaldesactus'); ?></label>
            <input type="number" id="carousel-speed" name="carousel_settings[speed]" value="<?php echo esc_attr($settings['speed']); ?>" class="widefat" min="1000" step="500">
        </p>
        
        <p>
            <label for="carousel-effect"><?php _e('Effet de transition:', 'lejournaldesactus'); ?></label>
            <select id="carousel-effect" name="carousel_settings[effect]" class="widefat">
                <option value="slide" <?php selected($settings['effect'], 'slide'); ?>><?php _e('Slide', 'lejournaldesactus'); ?></option>
                <option value="fade" <?php selected($settings['effect'], 'fade'); ?>><?php _e('Fondu', 'lejournaldesactus'); ?></option>
                <option value="cube" <?php selected($settings['effect'], 'cube'); ?>><?php _e('Cube', 'lejournaldesactus'); ?></option>
                <option value="coverflow" <?php selected($settings['effect'], 'coverflow'); ?>><?php _e('Coverflow', 'lejournaldesactus'); ?></option>
                <option value="flip" <?php selected($settings['effect'], 'flip'); ?>><?php _e('Flip', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="carousel-pagination"><?php _e('Pagination:', 'lejournaldesactus'); ?></label>
            <select id="carousel-pagination" name="carousel_settings[pagination]" class="widefat">
                <option value="yes" <?php selected($settings['pagination'], 'yes'); ?>><?php _e('Oui', 'lejournaldesactus'); ?></option>
                <option value="no" <?php selected($settings['pagination'], 'no'); ?>><?php _e('Non', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="carousel-navigation"><?php _e('Navigation:', 'lejournaldesactus'); ?></label>
            <select id="carousel-navigation" name="carousel_settings[navigation]" class="widefat">
                <option value="yes" <?php selected($settings['navigation'], 'yes'); ?>><?php _e('Oui', 'lejournaldesactus'); ?></option>
                <option value="no" <?php selected($settings['navigation'], 'no'); ?>><?php _e('Non', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="carousel-height"><?php _e('Hauteur:', 'lejournaldesactus'); ?></label>
            <select id="carousel-height" name="carousel_settings[height]" class="widefat">
                <option value="default" <?php selected($settings['height'], 'default'); ?>><?php _e('Par défaut', 'lejournaldesactus'); ?></option>
                <option value="small" <?php selected($settings['height'], 'small'); ?>><?php _e('Petit', 'lejournaldesactus'); ?></option>
                <option value="medium" <?php selected($settings['height'], 'medium'); ?>><?php _e('Moyen', 'lejournaldesactus'); ?></option>
                <option value="large" <?php selected($settings['height'], 'large'); ?>><?php _e('Grand', 'lejournaldesactus'); ?></option>
                <option value="full" <?php selected($settings['height'], 'full'); ?>><?php _e('Plein écran', 'lejournaldesactus'); ?></option>
            </select>
        </p>
        
        <div class="carousel-shortcode-info">
            <p><strong><?php _e('Shortcode:', 'lejournaldesactus'); ?></strong></p>
            <code>[lejournaldesactus_carousel id="<?php echo esc_attr($post->ID); ?>"]</code>
            <p class="description"><?php _e('Copiez ce shortcode et collez-le dans un article ou une page pour afficher ce carrousel.', 'lejournaldesactus'); ?></p>
        </div>
        <?php
    }

    /**
     * Enregistrer les métadonnées du carrousel
     */
    public function save_carousel_meta($post_id) {
        // Vérifier si c'est une sauvegarde automatique
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Vérifier le nonce
        if (!isset($_POST['lejournaldesactus_carousel_meta_nonce']) || !wp_verify_nonce($_POST['lejournaldesactus_carousel_meta_nonce'], 'lejournaldesactus_carousel_meta')) {
            return;
        }

        // Vérifier les permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Sauvegarder les slides
        if (isset($_POST['carousel_slides']) && is_array($_POST['carousel_slides'])) {
            $slides = array();
            foreach ($_POST['carousel_slides'] as $slide) {
                $slides[] = array(
                    'image_id' => absint($slide['image_id']),
                    'title' => sanitize_text_field($slide['title']),
                    'description' => wp_kses_post($slide['description']),
                    'button_text' => sanitize_text_field($slide['button_text']),
                    'button_url' => esc_url_raw($slide['button_url'])
                );
            }
            update_post_meta($post_id, '_lejournaldesactus_carousel_slides', $slides);
        }

        // Sauvegarder les paramètres
        if (isset($_POST['carousel_settings']) && is_array($_POST['carousel_settings'])) {
            $settings = array(
                'autoplay' => sanitize_text_field($_POST['carousel_settings']['autoplay']),
                'loop' => sanitize_text_field($_POST['carousel_settings']['loop']),
                'speed' => absint($_POST['carousel_settings']['speed']),
                'effect' => sanitize_text_field($_POST['carousel_settings']['effect']),
                'pagination' => sanitize_text_field($_POST['carousel_settings']['pagination']),
                'navigation' => sanitize_text_field($_POST['carousel_settings']['navigation']),
                'height' => sanitize_text_field($_POST['carousel_settings']['height'])
            );
            update_post_meta($post_id, '_lejournaldesactus_carousel_settings', $settings);
        }
    }

    /**
     * Ajouter les scripts pour l'administration
     */
    public function enqueue_admin_scripts($hook) {
        global $post_type;

        // N'ajouter les scripts que sur les pages d'édition des carrousels
        if (($hook == 'post.php' || $hook == 'post-new.php') && $post_type == 'lejournaldesactus_carousel') {
            wp_enqueue_media();
            
            wp_enqueue_script(
                'lejournaldesactus-carousel-admin',
                LEJOURNALDESACTUS_THEME_URI . '/assets/js/carousel-admin.js',
                array('jquery', 'wp-util'),
                LEJOURNALDESACTUS_VERSION,
                true
            );
            
            wp_enqueue_style(
                'lejournaldesactus-carousel-admin',
                LEJOURNALDESACTUS_THEME_URI . '/assets/css/carousel-admin.css',
                array(),
                LEJOURNALDESACTUS_VERSION
            );
        }
    }

    /**
     * Shortcode pour afficher un carrousel
     */
    public function carousel_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts, 'lejournaldesactus_carousel');

        $carousel_id = absint($atts['id']);
        if (!$carousel_id) {
            return '';
        }

        // Vérifier si le carrousel existe
        $carousel = get_post($carousel_id);
        if (!$carousel || $carousel->post_type !== 'lejournaldesactus_carousel') {
            return '';
        }

        // Récupérer les slides
        $slides = get_post_meta($carousel_id, '_lejournaldesactus_carousel_slides', true);
        if (empty($slides) || !is_array($slides)) {
            return '';
        }

        // Récupérer les paramètres
        $settings = get_post_meta($carousel_id, '_lejournaldesactus_carousel_settings', true);
        if (empty($settings) || !is_array($settings)) {
            $settings = array(
                'autoplay' => 'yes',
                'loop' => 'yes',
                'speed' => 3000,
                'effect' => 'slide',
                'pagination' => 'yes',
                'navigation' => 'yes',
                'height' => 'default'
            );
        }

        // Générer un ID unique pour ce carrousel
        $carousel_html_id = 'carousel-' . $carousel_id . '-' . uniqid();

        // Définir la classe de hauteur
        $height_class = '';
        switch ($settings['height']) {
            case 'small':
                $height_class = 'carousel-height-small';
                break;
            case 'medium':
                $height_class = 'carousel-height-medium';
                break;
            case 'large':
                $height_class = 'carousel-height-large';
                break;
            case 'full':
                $height_class = 'carousel-height-full';
                break;
            default:
                $height_class = 'carousel-height-default';
                break;
        }

        // Commencer la sortie
        ob_start();
        ?>
        <div id="<?php echo esc_attr($carousel_html_id); ?>" class="lejournaldesactus-carousel swiper-container <?php echo esc_attr($height_class); ?>">
            <div class="swiper-wrapper">
                <?php foreach ($slides as $slide) : ?>
                    <?php if (!empty($slide['image_id'])) : ?>
                        <div class="swiper-slide">
                            <?php 
                            $image_url = wp_get_attachment_image_url($slide['image_id'], 'full');
                            $image_alt = get_post_meta($slide['image_id'], '_wp_attachment_image_alt', true);
                            ?>
                            <div class="carousel-slide-image">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                            </div>
                            
                            <?php if (!empty($slide['title']) || !empty($slide['description']) || !empty($slide['button_text'])) : ?>
                                <div class="carousel-slide-content">
                                    <?php if (!empty($slide['title'])) : ?>
                                        <h2 class="carousel-slide-title"><?php echo esc_html($slide['title']); ?></h2>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($slide['description'])) : ?>
                                        <div class="carousel-slide-description"><?php echo wp_kses_post($slide['description']); ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($slide['button_text']) && !empty($slide['button_url'])) : ?>
                                        <div class="carousel-slide-button">
                                            <a href="<?php echo esc_url($slide['button_url']); ?>" class="btn btn-primary"><?php echo esc_html($slide['button_text']); ?></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <?php if ($settings['pagination'] === 'yes') : ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
            
            <?php if ($settings['navigation'] === 'yes') : ?>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            <?php endif; ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Swiper('#<?php echo esc_js($carousel_html_id); ?>', {
                    <?php if ($settings['autoplay'] === 'yes') : ?>
                    autoplay: {
                        delay: <?php echo esc_js($settings['speed']); ?>,
                        disableOnInteraction: false,
                    },
                    <?php endif; ?>
                    loop: <?php echo ($settings['loop'] === 'yes') ? 'true' : 'false'; ?>,
                    effect: '<?php echo esc_js($settings['effect']); ?>',
                    <?php if ($settings['pagination'] === 'yes') : ?>
                    pagination: {
                        el: '#<?php echo esc_js($carousel_html_id); ?> .swiper-pagination',
                        clickable: true,
                    },
                    <?php endif; ?>
                    <?php if ($settings['navigation'] === 'yes') : ?>
                    navigation: {
                        nextEl: '#<?php echo esc_js($carousel_html_id); ?> .swiper-button-next',
                        prevEl: '#<?php echo esc_js($carousel_html_id); ?> .swiper-button-prev',
                    },
                    <?php endif; ?>
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}

// Initialiser la classe
$lejournaldesactus_carousel = new Lejournaldesactus_Carousel();
