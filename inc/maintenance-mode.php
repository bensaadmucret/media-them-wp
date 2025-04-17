<?php
/**
 * Fonctionnalité de mode maintenance
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Ajouter les options de mode maintenance au Customizer
 */
function lejournaldesactus_maintenance_customize_register($wp_customize) {
    // Section pour le mode maintenance
    $wp_customize->add_section('lejournaldesactus_maintenance_section', array(
        'title'    => __('Mode Maintenance', 'lejournaldesactus'),
        'priority' => 150,
    ));
    
    // Option pour activer/désactiver le mode maintenance
    $wp_customize->add_setting('lejournaldesactus_maintenance_mode', array(
        'default'           => false,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_maintenance_mode', array(
        'label'       => __('Activer le mode maintenance', 'lejournaldesactus'),
        'description' => __('Lorsque cette option est activée, seuls les administrateurs connectés peuvent voir le site. Les autres visiteurs verront une page de maintenance.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_maintenance_section',
        'type'        => 'checkbox',
    ));
    
    // Option pour le titre de la page de maintenance
    $wp_customize->add_setting('lejournaldesactus_maintenance_title', array(
        'default'           => __('Site en maintenance', 'lejournaldesactus'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('lejournaldesactus_maintenance_title', array(
        'label'    => __('Titre de la page de maintenance', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_maintenance_section',
        'type'     => 'text',
    ));
    
    // Option pour le message de la page de maintenance
    $wp_customize->add_setting('lejournaldesactus_maintenance_message', array(
        'default'           => __('Notre site est actuellement en maintenance. Nous serons de retour très bientôt !', 'lejournaldesactus'),
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('lejournaldesactus_maintenance_message', array(
        'label'    => __('Message de la page de maintenance', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_maintenance_section',
        'type'     => 'textarea',
    ));
    
    // Option pour la date de retour
    $wp_customize->add_setting('lejournaldesactus_maintenance_date', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('lejournaldesactus_maintenance_date', array(
        'label'       => __('Date de retour (optionnel)', 'lejournaldesactus'),
        'description' => __('Format: YYYY-MM-DD HH:MM:SS (ex: 2025-12-31 23:59:59)', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_maintenance_section',
        'type'        => 'text',
    ));
    
    // Option pour l'image de fond
    $wp_customize->add_setting('lejournaldesactus_maintenance_background', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'lejournaldesactus_maintenance_background', array(
        'label'    => __('Image de fond', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_maintenance_section',
        'settings' => 'lejournaldesactus_maintenance_background',
    )));
    
    // Option pour afficher un formulaire de contact
    $wp_customize->add_setting('lejournaldesactus_maintenance_contact_form', array(
        'default'           => false,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_maintenance_contact_form', array(
        'label'    => __('Afficher un formulaire de contact', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_maintenance_section',
        'type'     => 'checkbox',
    ));
    
    // Option pour l'email de contact
    $wp_customize->add_setting('lejournaldesactus_maintenance_email', array(
        'default'           => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('lejournaldesactus_maintenance_email', array(
        'label'       => __('Email de contact', 'lejournaldesactus'),
        'description' => __('Les messages du formulaire de contact seront envoyés à cette adresse.', 'lejournaldesactus'),
        'section'     => 'lejournaldesactus_maintenance_section',
        'type'        => 'email',
    ));
    
    // Option pour les liens sociaux
    $wp_customize->add_setting('lejournaldesactus_maintenance_social_links', array(
        'default'           => false,
        'sanitize_callback' => 'lejournaldesactus_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('lejournaldesactus_maintenance_social_links', array(
        'label'    => __('Afficher les liens sociaux', 'lejournaldesactus'),
        'section'  => 'lejournaldesactus_maintenance_section',
        'type'     => 'checkbox',
    ));
}
// add_action('customize_register', 'lejournaldesactus_maintenance_customize_register');

/**
 * Fonction de validation pour les cases à cocher
 */
if (!function_exists('lejournaldesactus_sanitize_checkbox')) {
function lejournaldesactus_sanitize_checkbox($input) {
    return (isset($input) && true == $input) ? true : false;
}
}

/**
 * Afficher la page de maintenance
 */
function lejournaldesactus_display_maintenance_page() {
    // Vérifier si le mode maintenance est activé
    if (!get_theme_mod('lejournaldesactus_maintenance_mode', false)) {
        return;
    }
    
    // Ne pas afficher la page de maintenance pour les administrateurs connectés
    if (current_user_can('manage_options') && is_user_logged_in()) {
        return;
    }
    
    // Ne pas afficher la page de maintenance sur la page de connexion
    if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
        return;
    }
    
    // Permettre aux outils de test WordPress de fonctionner
    if (isset($_SERVER['HTTP_USER_AGENT']) && (
        strpos($_SERVER['HTTP_USER_AGENT'], 'WordPress') !== false ||
        strpos($_SERVER['HTTP_USER_AGENT'], 'wp-health-check') !== false
    )) {
        return;
    }
    
    // Ne pas afficher la page de maintenance sur la page de connexion
    if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
        return;
    }
    
    // Récupérer les options
    $title = get_theme_mod('lejournaldesactus_maintenance_title', __('Site en maintenance', 'lejournaldesactus'));
    $message = get_theme_mod('lejournaldesactus_maintenance_message', __('Notre site est actuellement en maintenance. Nous serons de retour très bientôt !', 'lejournaldesactus'));
    $date = get_theme_mod('lejournaldesactus_maintenance_date', '');
    $background = get_theme_mod('lejournaldesactus_maintenance_background', '');
    $show_contact = get_theme_mod('lejournaldesactus_maintenance_contact_form', false);
    $show_social = get_theme_mod('lejournaldesactus_maintenance_social_links', false);
    
    // Définir le statut HTTP 503 (Service Unavailable)
    status_header(503);
    
    // Ajouter l'en-tête Retry-After
    header('Retry-After: 3600'); // 1 heure
    
    // Ajouter un header pour permettre aux outils de test de cache de fonctionner
    header('X-Cache-Enabled: false');
    
    // Afficher la page de maintenance
    ?><!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html($title); ?> - <?php bloginfo('name'); ?></title>
        <link rel="stylesheet" href="<?php echo esc_url(LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap/css/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo esc_url(LEJOURNALDESACTUS_THEME_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>">
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                color: #333;
                line-height: 1.6;
                background-color: #f8f9fa;
                <?php if ($background) : ?>
                background-image: url('<?php echo esc_url($background); ?>');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                <?php endif; ?>
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
            }
            
            .maintenance-container {
                max-width: 800px;
                background-color: rgba(255, 255, 255, 0.95);
                border-radius: 10px;
                box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
                padding: 40px;
                text-align: center;
            }
            
            .maintenance-logo {
                margin-bottom: 30px;
            }
            
            .maintenance-logo img {
                max-width: 200px;
                height: auto;
            }
            
            .maintenance-title {
                font-size: 2.5rem;
                margin-bottom: 20px;
                color: #333;
            }
            
            .maintenance-message {
                font-size: 1.2rem;
                margin-bottom: 30px;
            }
            
            .countdown {
                display: flex;
                justify-content: center;
                margin: 30px 0;
            }
            
            .countdown-item {
                margin: 0 15px;
                text-align: center;
            }
            
            .countdown-number {
                font-size: 2.5rem;
                font-weight: bold;
                color: #007bff;
                display: block;
            }
            
            .countdown-label {
                font-size: 0.9rem;
                text-transform: uppercase;
                color: #6c757d;
            }
            
            .social-links {
                margin-top: 30px;
            }
            
            .social-links a {
                display: inline-block;
                margin: 0 10px;
                font-size: 1.5rem;
                color: #007bff;
                transition: color 0.3s;
            }
            
            .social-links a:hover {
                color: #0056b3;
            }
            
            .contact-form {
                margin-top: 40px;
                text-align: left;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-control {
                border-radius: 5px;
                padding: 12px;
                border: 1px solid #ced4da;
            }
            
            .btn-primary {
                background-color: #007bff;
                border-color: #007bff;
                padding: 12px 25px;
                border-radius: 5px;
                font-weight: 600;
                transition: all 0.3s;
            }
            
            .btn-primary:hover {
                background-color: #0056b3;
                border-color: #0056b3;
            }
            
            @media (max-width: 768px) {
                .maintenance-title {
                    font-size: 2rem;
                }
                
                .maintenance-message {
                    font-size: 1rem;
                }
                
                .countdown-number {
                    font-size: 1.8rem;
                }
                
                .maintenance-container {
                    padding: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="maintenance-container">
            <div class="maintenance-logo">
                <?php 
                $custom_logo_id = get_theme_mod('custom_logo');
                $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                if ($logo) {
                    echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '">';
                } else {
                    echo '<h2>' . get_bloginfo('name') . '</h2>';
                }
                ?>
            </div>
            
            <h1 class="maintenance-title"><?php echo esc_html($title); ?></h1>
            <div class="maintenance-message"><?php echo wp_kses_post($message); ?></div>
            
            <?php if ($date) : ?>
            <div class="countdown" id="countdown" data-date="<?php echo esc_attr($date); ?>">
                <div class="countdown-item">
                    <span class="countdown-number" id="countdown-days">0</span>
                    <span class="countdown-label"><?php _e('Jours', 'lejournaldesactus'); ?></span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="countdown-hours">0</span>
                    <span class="countdown-label"><?php _e('Heures', 'lejournaldesactus'); ?></span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="countdown-minutes">0</span>
                    <span class="countdown-label"><?php _e('Minutes', 'lejournaldesactus'); ?></span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="countdown-seconds">0</span>
                    <span class="countdown-label"><?php _e('Secondes', 'lejournaldesactus'); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($show_contact) : ?>
            <div class="contact-form">
                <h3><?php _e('Contactez-nous', 'lejournaldesactus'); ?></h3>
                <form id="maintenance-contact-form" method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" id="name" placeholder="<?php _e('Votre nom', 'lejournaldesactus'); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" id="email" placeholder="<?php _e('Votre email', 'lejournaldesactus'); ?>" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="message" id="message" rows="4" placeholder="<?php _e('Votre message', 'lejournaldesactus'); ?>" required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><?php _e('Envoyer', 'lejournaldesactus'); ?></button>
                        <div id="form-status"></div>
                    </div>
                    <?php wp_nonce_field('maintenance_contact_nonce', 'maintenance_nonce'); ?>
                </form>
            </div>
            <?php endif; ?>
            
            <?php if ($show_social) : ?>
            <div class="social-links">
                <?php
                $social_networks = array(
                    'facebook' => array('icon' => 'bi-facebook', 'url' => get_theme_mod('lejournaldesactus_facebook_url', '')),
                    'twitter' => array('icon' => 'bi-twitter-x', 'url' => get_theme_mod('lejournaldesactus_twitter_url', '')),
                    'instagram' => array('icon' => 'bi-instagram', 'url' => get_theme_mod('lejournaldesactus_instagram_url', '')),
                    'linkedin' => array('icon' => 'bi-linkedin', 'url' => get_theme_mod('lejournaldesactus_linkedin_url', '')),
                    'youtube' => array('icon' => 'bi-youtube', 'url' => get_theme_mod('lejournaldesactus_youtube_url', '')),
                );
                
                foreach ($social_networks as $network => $data) {
                    if (!empty($data['url'])) {
                        echo '<a href="' . esc_url($data['url']) . '" target="_blank" rel="noopener noreferrer"><i class="bi ' . esc_attr($data['icon']) . '"></i></a>';
                    }
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($date) : ?>
        <script>
            // Compte à rebours
            document.addEventListener('DOMContentLoaded', function() {
                const countdownElement = document.getElementById('countdown');
                const targetDate = new Date(countdownElement.dataset.date).getTime();
                
                const countdownDays = document.getElementById('countdown-days');
                const countdownHours = document.getElementById('countdown-hours');
                const countdownMinutes = document.getElementById('countdown-minutes');
                const countdownSeconds = document.getElementById('countdown-seconds');
                
                function updateCountdown() {
                    const now = new Date().getTime();
                    const distance = targetDate - now;
                    
                    if (distance < 0) {
                        countdownDays.textContent = '0';
                        countdownHours.textContent = '0';
                        countdownMinutes.textContent = '0';
                        countdownSeconds.textContent = '0';
                        return;
                    }
                    
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    
                    countdownDays.textContent = days;
                    countdownHours.textContent = hours;
                    countdownMinutes.textContent = minutes;
                    countdownSeconds.textContent = seconds;
                }
                
                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
        </script>
        <?php endif; ?>
        
        <?php if ($show_contact) : ?>
        <script>
            // Formulaire de contact
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('maintenance-contact-form');
                const formStatus = document.getElementById('form-status');
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    formData.append('action', 'maintenance_contact_form');
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            formStatus.innerHTML = '<div class="alert alert-success mt-3">' + data.data + '</div>';
                            form.reset();
                        } else {
                            formStatus.innerHTML = '<div class="alert alert-danger mt-3">' + data.data + '</div>';
                        }
                    })
                    .catch(error => {
                        formStatus.innerHTML = '<div class="alert alert-danger mt-3">Une erreur s\'est produite. Veuillez réessayer.</div>';
                    });
                });
            });
        </script>
        <?php endif; ?>
    </body>
    </html>
    <?php
    exit;
}
add_action('template_redirect', 'lejournaldesactus_display_maintenance_page');

/**
 * Traiter le formulaire de contact du mode maintenance
 */
function lejournaldesactus_maintenance_contact_form() {
    // Vérifier le nonce
    if (!isset($_POST['maintenance_nonce']) || !wp_verify_nonce($_POST['maintenance_nonce'], 'maintenance_contact_nonce')) {
        wp_send_json_error(__('Vérification de sécurité échouée.', 'lejournaldesactus'));
    }
    
    // Récupérer les données du formulaire
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    
    // Vérifier les données
    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error(__('Veuillez remplir tous les champs.', 'lejournaldesactus'));
    }
    
    // Récupérer l'email de destination
    $to = get_theme_mod('lejournaldesactus_maintenance_email', get_option('admin_email'));
    
    // Construire l'email
    $subject = sprintf(__('[%s] Message du mode maintenance', 'lejournaldesactus'), get_bloginfo('name'));
    
    $body = sprintf(__('Nom: %s', 'lejournaldesactus'), $name) . "\r\n\r\n";
    $body .= sprintf(__('Email: %s', 'lejournaldesactus'), $email) . "\r\n\r\n";
    $body .= sprintf(__('Message: %s', 'lejournaldesactus'), $message) . "\r\n\r\n";
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $name . ' <' . $email . '>',
        'Reply-To: ' . $email,
    );
    
    // Envoyer l'email
    $sent = wp_mail($to, $subject, $body, $headers);
    
    if ($sent) {
        wp_send_json_success(__('Votre message a été envoyé avec succès. Nous vous répondrons dès que possible.', 'lejournaldesactus'));
    } else {
        wp_send_json_error(__('Une erreur s\'est produite lors de l\'envoi de votre message. Veuillez réessayer.', 'lejournaldesactus'));
    }
}
add_action('wp_ajax_maintenance_contact_form', 'lejournaldesactus_maintenance_contact_form');
add_action('wp_ajax_nopriv_maintenance_contact_form', 'lejournaldesactus_maintenance_contact_form');

/**
 * Ajouter une barre d'administration pour les administrateurs en mode maintenance
 */
function lejournaldesactus_maintenance_admin_bar($wp_admin_bar) {
    if (!get_theme_mod('lejournaldesactus_maintenance_mode', false) || !current_user_can('manage_options')) {
        return;
    }
    
    $args = array(
        'id'    => 'maintenance-mode',
        'title' => '<span class="maintenance-mode-text">' . __('MODE MAINTENANCE ACTIF', 'lejournaldesactus') . '</span>',
        'href'  => admin_url('customize.php?autofocus[section]=lejournaldesactus_maintenance_section'),
        'meta'  => array(
            'class' => 'maintenance-mode-active',
            'title' => __('Le site est actuellement en mode maintenance. Cliquez pour modifier les paramètres.', 'lejournaldesactus')
        )
    );
    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'lejournaldesactus_maintenance_admin_bar', 100);

/**
 * Ajouter un style pour la barre d'administration en mode maintenance
 */
function lejournaldesactus_maintenance_admin_bar_style() {
    if (!get_theme_mod('lejournaldesactus_maintenance_mode', false) || !current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <style>
        #wpadminbar #wp-admin-bar-maintenance-mode {
            background-color: #f44336;
        }
        #wpadminbar #wp-admin-bar-maintenance-mode .ab-item {
            color: #ffffff !important;
        }
        #wpadminbar #wp-admin-bar-maintenance-mode:hover .ab-item {
            background-color: #d32f2f !important;
        }
        #wpadminbar #wp-admin-bar-maintenance-mode .maintenance-mode-text {
            color: #ffffff !important;
            font-weight: bold;
        }
    </style>
    <?php
}
add_action('wp_head', 'lejournaldesactus_maintenance_admin_bar_style');
add_action('admin_head', 'lejournaldesactus_maintenance_admin_bar_style');
