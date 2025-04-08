<?php
/**
 * Gestion de la conformité RGPD
 *
 * @package LeJournalDesActus
 */

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de gestion de la conformité RGPD
 */
class LeJournalDesActus_RGPD {

    /**
     * Initialisation de la classe
     */
    public static function init() {
        // Ajouter la bannière de cookies
        add_action('wp_footer', array(__CLASS__, 'cookie_banner'));
        
        // Ajouter les scripts et styles
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        
        // Ajouter la page de politique de confidentialité
        add_action('init', array(__CLASS__, 'register_privacy_page'));
        
        // Ajouter une page dans l'administration
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        
        // Ajouter les options dans le customizer
        add_action('customize_register', array(__CLASS__, 'customize_register'));
        
        // Ajouter le shortcode pour le formulaire de demande d'accès aux données
        add_shortcode('lejournaldesactus_data_request', array(__CLASS__, 'data_request_shortcode'));
        
        // Traitement des demandes d'accès aux données
        add_action('wp_ajax_lejournaldesactus_data_request', array(__CLASS__, 'process_data_request'));
        add_action('wp_ajax_nopriv_lejournaldesactus_data_request', array(__CLASS__, 'process_data_request'));
    }

    /**
     * Enregistrer les scripts et styles
     */
    public static function enqueue_scripts() {
        wp_enqueue_style(
            'lejournaldesactus-rgpd',
            get_template_directory_uri() . '/assets/css/rgpd.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/rgpd.css')
        );
        
        wp_enqueue_script(
            'lejournaldesactus-rgpd',
            get_template_directory_uri() . '/assets/js/rgpd.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/rgpd.js'),
            true
        );
        
        wp_localize_script(
            'lejournaldesactus-rgpd',
            'lejournaldesactus_rgpd',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'cookie_expiry' => apply_filters('lejournaldesactus_cookie_expiry', 365),
                'cookie_name' => apply_filters('lejournaldesactus_cookie_name', 'lejournaldesactus_cookies_accepted')
            )
        );
    }

    /**
     * Afficher la bannière de cookies
     */
    public static function cookie_banner() {
        // Récupération des options
        $cookie_text = get_theme_mod('lejournaldesactus_cookie_text', __('Nous utilisons des cookies pour améliorer votre expérience sur notre site. En continuant à naviguer, vous acceptez notre utilisation des cookies.', 'lejournaldesactus'));
        $cookie_button = get_theme_mod('lejournaldesactus_cookie_button', __('J\'accepte', 'lejournaldesactus'));
        $privacy_link = get_theme_mod('lejournaldesactus_privacy_link', __('Politique de confidentialité', 'lejournaldesactus'));
        $privacy_page = get_theme_mod('lejournaldesactus_privacy_page', 0);
        
        // Affichage de la bannière
        ?>
        <div id="cookie-banner" class="cookie-banner">
            <div class="cookie-content">
                <p><?php echo esc_html($cookie_text); ?></p>
                <div class="cookie-actions">
                    <button id="accept-cookies" class="accept-cookies"><?php echo esc_html($cookie_button); ?></button>
                    <?php if ($privacy_page) : ?>
                        <a href="<?php echo esc_url(get_permalink($privacy_page)); ?>" class="privacy-link"><?php echo esc_html($privacy_link); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Enregistrer la page de politique de confidentialité
     */
    public static function register_privacy_page() {
        // Vérifier si la page existe déjà
        $privacy_page = get_theme_mod('lejournaldesactus_privacy_page', 0);
        
        if (!$privacy_page || !get_post($privacy_page)) {
            // Créer la page de politique de confidentialité
            $privacy_content = '<!-- wp:heading {"level":1} -->
<h1>Politique de confidentialité</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cette politique de confidentialité décrit comment nous recueillons, utilisons et partageons vos informations personnelles lorsque vous visitez notre site.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Informations que nous collectons</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorsque vous visitez notre site, nous collectons automatiquement certaines informations sur votre appareil, notamment des informations sur votre navigateur web, votre adresse IP, votre fuseau horaire et certains des cookies qui sont installés sur votre appareil.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Lorsque vous naviguez sur le site, nous recueillons également des informations sur les pages web ou les produits que vous consultez, les sites web ou les termes de recherche qui vous ont redirigé vers notre site, et des informations sur la façon dont vous interagissez avec le site.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Comment nous utilisons vos informations</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nous utilisons les informations que nous recueillons pour :</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>
<li>Gérer notre site web</li>
<li>Améliorer notre site web</li>
<li>Comprendre l\'efficacité de notre marketing</li>
<li>Communiquer avec vous</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>Vos droits</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Si vous êtes résident européen, vous avez le droit d\'accéder aux informations personnelles que nous détenons à votre sujet et de demander que vos informations personnelles soient corrigées, mises à jour ou supprimées. Si vous souhaitez exercer ce droit, veuillez nous contacter.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Cookies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Les cookies sont des fichiers contenant une petite quantité de données qui sont couramment utilisés comme identifiant unique anonyme. Ils sont envoyés à votre navigateur par le site web que vous visitez et sont stockés sur le disque dur de votre ordinateur.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Notre site web utilise ces "cookies" pour collecter des informations et améliorer notre service. Vous avez la possibilité d\'accepter ou de refuser ces cookies et de savoir quand un cookie est envoyé à votre ordinateur.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Modifications de cette politique de confidentialité</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. Les modifications et clarifications prendront effet immédiatement après leur publication sur le site web.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Nous contacter</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Pour toute question concernant cette politique de confidentialité, veuillez nous contacter.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[lejournaldesactus_data_request]
<!-- /wp:shortcode -->';
            
            // Créer la page
            $privacy_page_id = wp_insert_post(array(
                'post_title' => __('Politique de confidentialité', 'lejournaldesactus'),
                'post_content' => $privacy_content,
                'post_status' => 'publish',
                'post_type' => 'page',
                'comment_status' => 'closed'
            ));
            
            // Enregistrer l'ID de la page dans les options du thème
            if ($privacy_page_id && !is_wp_error($privacy_page_id)) {
                set_theme_mod('lejournaldesactus_privacy_page', $privacy_page_id);
            }
        }
    }

    /**
     * Ajouter les options dans le customizer
     */
    public static function customize_register($wp_customize) {
        // Section RGPD
        $wp_customize->add_section('lejournaldesactus_rgpd', array(
            'title' => __('RGPD & Confidentialité', 'lejournaldesactus'),
            'priority' => 120
        ));
        
        // Texte de la bannière de cookies
        $wp_customize->add_setting('lejournaldesactus_cookie_text', array(
            'default' => __('Nous utilisons des cookies pour améliorer votre expérience sur notre site. En continuant à naviguer, vous acceptez notre utilisation des cookies.', 'lejournaldesactus'),
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        $wp_customize->add_control('lejournaldesactus_cookie_text', array(
            'label' => __('Texte de la bannière de cookies', 'lejournaldesactus'),
            'section' => 'lejournaldesactus_rgpd',
            'type' => 'textarea'
        ));
        
        // Texte du bouton d'acceptation
        $wp_customize->add_setting('lejournaldesactus_cookie_button', array(
            'default' => __('J\'accepte', 'lejournaldesactus'),
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        $wp_customize->add_control('lejournaldesactus_cookie_button', array(
            'label' => __('Texte du bouton d\'acceptation', 'lejournaldesactus'),
            'section' => 'lejournaldesactus_rgpd',
            'type' => 'text'
        ));
        
        // Texte du lien vers la politique de confidentialité
        $wp_customize->add_setting('lejournaldesactus_privacy_link', array(
            'default' => __('Politique de confidentialité', 'lejournaldesactus'),
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        $wp_customize->add_control('lejournaldesactus_privacy_link', array(
            'label' => __('Texte du lien vers la politique de confidentialité', 'lejournaldesactus'),
            'section' => 'lejournaldesactus_rgpd',
            'type' => 'text'
        ));
        
        // Page de politique de confidentialité
        $wp_customize->add_setting('lejournaldesactus_privacy_page', array(
            'default' => 0,
            'sanitize_callback' => 'absint'
        ));
        
        $wp_customize->add_control('lejournaldesactus_privacy_page', array(
            'label' => __('Page de politique de confidentialité', 'lejournaldesactus'),
            'section' => 'lejournaldesactus_rgpd',
            'type' => 'dropdown-pages'
        ));
    }

    /**
     * Ajouter une page dans l'administration
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'options-general.php',
            __('Paramètres RGPD', 'lejournaldesactus'),
            __('RGPD', 'lejournaldesactus'),
            'manage_options',
            'lejournaldesactus-rgpd',
            array(__CLASS__, 'admin_page')
        );
    }

    /**
     * Page d'administration pour les paramètres RGPD
     */
    public static function admin_page() {
        // Traitement du formulaire
        if (isset($_POST['lejournaldesactus_rgpd_nonce']) && wp_verify_nonce($_POST['lejournaldesactus_rgpd_nonce'], 'lejournaldesactus_rgpd_settings')) {
            // Mise à jour des options
            if (isset($_POST['privacy_page'])) {
                set_theme_mod('lejournaldesactus_privacy_page', absint($_POST['privacy_page']));
            }
            
            if (isset($_POST['cookie_text'])) {
                set_theme_mod('lejournaldesactus_cookie_text', sanitize_text_field($_POST['cookie_text']));
            }
            
            if (isset($_POST['cookie_button'])) {
                set_theme_mod('lejournaldesactus_cookie_button', sanitize_text_field($_POST['cookie_button']));
            }
            
            if (isset($_POST['privacy_link'])) {
                set_theme_mod('lejournaldesactus_privacy_link', sanitize_text_field($_POST['privacy_link']));
            }
            
            echo '<div class="notice notice-success"><p>' . __('Paramètres enregistrés avec succès.', 'lejournaldesactus') . '</p></div>';
        }
        
        // Récupération des options
        $privacy_page = get_theme_mod('lejournaldesactus_privacy_page', 0);
        $cookie_text = get_theme_mod('lejournaldesactus_cookie_text', __('Nous utilisons des cookies pour améliorer votre expérience sur notre site. En continuant à naviguer, vous acceptez notre utilisation des cookies.', 'lejournaldesactus'));
        $cookie_button = get_theme_mod('lejournaldesactus_cookie_button', __('J\'accepte', 'lejournaldesactus'));
        $privacy_link = get_theme_mod('lejournaldesactus_privacy_link', __('Politique de confidentialité', 'lejournaldesactus'));
        
        // Affichage de la page
        ?>
        <div class="wrap">
            <h1><?php _e('Paramètres RGPD', 'lejournaldesactus'); ?></h1>
            
            <form method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Page de politique de confidentialité', 'lejournaldesactus'); ?></th>
                        <td>
                            <?php
                            wp_dropdown_pages(array(
                                'name' => 'privacy_page',
                                'show_option_none' => __('Sélectionner une page', 'lejournaldesactus'),
                                'option_none_value' => '0',
                                'selected' => $privacy_page
                            ));
                            ?>
                            <p class="description"><?php _e('Sélectionnez la page qui contient votre politique de confidentialité.', 'lejournaldesactus'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Texte de la bannière de cookies', 'lejournaldesactus'); ?></th>
                        <td>
                            <textarea name="cookie_text" rows="3" class="large-text"><?php echo esc_textarea($cookie_text); ?></textarea>
                            <p class="description"><?php _e('Ce texte sera affiché dans la bannière de cookies.', 'lejournaldesactus'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Texte du bouton d\'acceptation', 'lejournaldesactus'); ?></th>
                        <td>
                            <input type="text" name="cookie_button" value="<?php echo esc_attr($cookie_button); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Texte du lien vers la politique de confidentialité', 'lejournaldesactus'); ?></th>
                        <td>
                            <input type="text" name="privacy_link" value="<?php echo esc_attr($privacy_link); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
                
                <?php wp_nonce_field('lejournaldesactus_rgpd_settings', 'lejournaldesactus_rgpd_nonce'); ?>
                <p class="submit">
                    <input type="submit" class="button button-primary" value="<?php esc_attr_e('Enregistrer les paramètres', 'lejournaldesactus'); ?>">
                </p>
            </form>
            
            <div class="rgpd-info">
                <h2><?php _e('Informations sur le RGPD', 'lejournaldesactus'); ?></h2>
                <p><?php _e('Le Règlement Général sur la Protection des Données (RGPD) est une réglementation de l\'Union européenne qui renforce la protection des données personnelles des individus.', 'lejournaldesactus'); ?></p>
                <p><?php _e('Voici quelques points importants à prendre en compte :', 'lejournaldesactus'); ?></p>
                <ul>
                    <li><?php _e('Assurez-vous que votre politique de confidentialité est à jour et conforme au RGPD.', 'lejournaldesactus'); ?></li>
                    <li><?php _e('Obtenez un consentement explicite avant de collecter des données personnelles.', 'lejournaldesactus'); ?></li>
                    <li><?php _e('Permettez aux utilisateurs d\'accéder à leurs données et de les supprimer si nécessaire.', 'lejournaldesactus'); ?></li>
                    <li><?php _e('Informez les utilisateurs en cas de violation de données.', 'lejournaldesactus'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Shortcode pour le formulaire de demande d'accès aux données
     */
    public static function data_request_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'title' => __('Demande d\'accès à vos données personnelles', 'lejournaldesactus')
            ),
            $atts,
            'lejournaldesactus_data_request'
        );
        
        ob_start();
        ?>
        <div class="rgpd-data-request">
            <h3><?php echo esc_html($atts['title']); ?></h3>
            <p><?php _e('Conformément au RGPD, vous pouvez demander l\'accès, la rectification ou la suppression de vos données personnelles. Veuillez remplir le formulaire ci-dessous pour effectuer une demande.', 'lejournaldesactus'); ?></p>
            
            <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" class="data-request-form">
                <div class="form-group">
                    <label for="name"><?php _e('Votre nom', 'lejournaldesactus'); ?></label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="email"><?php _e('Votre email', 'lejournaldesactus'); ?></label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="request_type"><?php _e('Type de demande', 'lejournaldesactus'); ?></label>
                    <select name="request_type" id="request_type" required>
                        <option value=""><?php _e('Sélectionner', 'lejournaldesactus'); ?></option>
                        <option value="access"><?php _e('Accès à mes données', 'lejournaldesactus'); ?></option>
                        <option value="rectify"><?php _e('Rectification de mes données', 'lejournaldesactus'); ?></option>
                        <option value="delete"><?php _e('Suppression de mes données', 'lejournaldesactus'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message"><?php _e('Détails de votre demande', 'lejournaldesactus'); ?></label>
                    <textarea name="message" id="message" rows="5" required></textarea>
                </div>
                <div class="form-group consent-group">
                    <label>
                        <input type="checkbox" name="consent" value="yes" required>
                        <?php _e('Je confirme que les informations fournies sont exactes et que je suis la personne concernée par cette demande.', 'lejournaldesactus'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <button type="submit"><?php _e('Envoyer la demande', 'lejournaldesactus'); ?></button>
                </div>
                <div class="data-request-message"></div>
                
                <input type="hidden" name="action" value="lejournaldesactus_data_request">
                <?php wp_nonce_field('data_request_nonce', 'data_request_nonce'); ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Traitement des demandes d'accès aux données
     */
    public static function process_data_request() {
        // Vérification du nonce pour la sécurité
        if (!isset($_POST['data_request_nonce']) || !wp_verify_nonce($_POST['data_request_nonce'], 'data_request_nonce')) {
            wp_send_json_error(array('message' => __('Erreur de sécurité. Veuillez réessayer.', 'lejournaldesactus')));
            exit;
        }
        
        // Vérification du consentement
        if (!isset($_POST['consent']) || $_POST['consent'] !== 'yes') {
            wp_send_json_error(array('message' => __('Vous devez confirmer que les informations fournies sont exactes.', 'lejournaldesactus')));
            exit;
        }
        
        // Récupération et nettoyage des données
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $request_type = sanitize_text_field($_POST['request_type']);
        $message = sanitize_textarea_field($_POST['message']);
        
        // Validation de l'email
        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Veuillez fournir une adresse email valide.', 'lejournaldesactus')));
            exit;
        }
        
        // Préparation du message pour l'administrateur
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        
        $subject = sprintf(__('[%s] Demande RGPD : %s', 'lejournaldesactus'), $site_name, self::get_request_type_label($request_type));
        
        $admin_message = sprintf(
            __('Une nouvelle demande RGPD a été soumise sur votre site %s.

Détails de la demande :
Nom : %s
Email : %s
Type de demande : %s
Message : %s

Veuillez traiter cette demande conformément au RGPD.', 'lejournaldesactus'),
            $site_name,
            $name,
            $email,
            self::get_request_type_label($request_type),
            $message
        );
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        // Envoi de l'email à l'administrateur
        $email_sent = wp_mail($admin_email, $subject, $admin_message, $headers);
        
        if (!$email_sent) {
            wp_send_json_error(array('message' => __('Une erreur est survenue lors de l\'envoi de votre demande. Veuillez réessayer.', 'lejournaldesactus')));
            exit;
        }
        
        // Envoi d'une confirmation à l'utilisateur
        $user_subject = sprintf(__('[%s] Confirmation de votre demande RGPD', 'lejournaldesactus'), $site_name);
        
        $user_message = sprintf(
            __('Bonjour %s,

Nous avons bien reçu votre demande RGPD de type "%s".

Notre équipe va traiter votre demande dans les plus brefs délais et vous contactera si nécessaire.

Cordialement,
L\'équipe de %s', 'lejournaldesactus'),
            $name,
            self::get_request_type_label($request_type),
            $site_name
        );
        
        wp_mail($email, $user_subject, $user_message, $headers);
        
        wp_send_json_success(array('message' => __('Votre demande a été envoyée avec succès. Nous la traiterons dans les plus brefs délais.', 'lejournaldesactus')));
        exit;
    }

    /**
     * Obtenir le libellé du type de demande
     */
    private static function get_request_type_label($request_type) {
        switch ($request_type) {
            case 'access':
                return __('Accès à mes données', 'lejournaldesactus');
            case 'rectify':
                return __('Rectification de mes données', 'lejournaldesactus');
            case 'delete':
                return __('Suppression de mes données', 'lejournaldesactus');
            default:
                return __('Autre', 'lejournaldesactus');
        }
    }
}

// Initialisation de la classe
LeJournalDesActus_RGPD::init();