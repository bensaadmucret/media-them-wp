<?php
/**
 * Gestion de la newsletter
 *
 * @package LeJournalDesActus
 */

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de gestion de la newsletter
 */
class LeJournalDesActus_Newsletter {

    /**
     * Initialisation de la classe
     */
    public static function init() {
        // Création de la table lors de l'activation du thème
        add_action('after_switch_theme', array(__CLASS__, 'create_newsletter_table'));
        
        // Traitement du formulaire d'inscription via AJAX (pour compatibilité)
        add_action('wp_ajax_lejournaldesactus_newsletter_subscribe', array(__CLASS__, 'process_subscription'));
        add_action('wp_ajax_nopriv_lejournaldesactus_newsletter_subscribe', array(__CLASS__, 'process_subscription'));
        
        // Ajout d'un point de terminaison personnalisé pour le traitement de la newsletter
        add_action('init', array(__CLASS__, 'add_newsletter_endpoint'));
        
        // Confirmation d'inscription (double opt-in)
        add_action('init', array(__CLASS__, 'confirm_subscription'));
        
        // Traitement du désabonnement
        add_action('init', array(__CLASS__, 'unsubscribe_subscription'));
        
        // Shortcode pour afficher le formulaire d'inscription
        add_shortcode('lejournaldesactus_newsletter', array(__CLASS__, 'newsletter_shortcode'));
        
        // Ajouter une page dans l'administration
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        
        // Enregistrer les scripts et styles
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        
        // Flush les règles de réécriture lors de l'activation du thème
        add_action('after_switch_theme', array(__CLASS__, 'flush_rewrite_rules'));
    }

    /**
     * Enregistrer les scripts et styles
     */
    public static function enqueue_scripts() {
        wp_enqueue_style(
            'lejournaldesactus-newsletter',
            get_template_directory_uri() . '/assets/css/newsletter.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/newsletter.css')
        );
        
        wp_enqueue_script(
            'lejournaldesactus-newsletter',
            get_template_directory_uri() . '/assets/js/newsletter.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/newsletter.js'),
            true
        );
        
        wp_localize_script(
            'lejournaldesactus-newsletter',
            'lejournaldesactus_newsletter',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'direct_url' => get_template_directory_uri() . '/process-newsletter.php',
                'submitting' => __('Envoi en cours...', 'lejournaldesactus'),
                'error' => __('Une erreur est survenue. Veuillez réessayer.', 'lejournaldesactus'),
                'success' => __('Merci pour votre inscription !', 'lejournaldesactus'),
                'consent_required' => __('Vous devez accepter la politique de confidentialité pour vous inscrire.', 'lejournaldesactus')
            )
        );
    }

    /**
     * Création de la table pour stocker les abonnés
     */
    public static function create_newsletter_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            confirmation_key varchar(50) NOT NULL,
            created_at datetime NOT NULL,
            confirmed_at datetime DEFAULT NULL,
            unsubscribed_at datetime DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            gdpr_consent tinyint(1) NOT NULL DEFAULT 0,
            immediate_notifications tinyint(1) NOT NULL DEFAULT 0,
            consent_text text DEFAULT NULL,
            preferred_categories text DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Débogage - Vérification de la création de la table
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        error_log('Table newsletter ' . ($table_exists ? 'existe' : 'n\'existe pas') . ' après création/vérification');
        
        if ($table_exists) {
            // Vérifier si les colonnes existent
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
            $column_names = array_map(function($col) { return $col->Field; }, $columns);
            
            error_log('Colonnes existantes dans la table newsletter: ' . implode(', ', $column_names));
            
            // Vérifier si les colonnes consent_text et preferred_categories existent
            if (!in_array('consent_text', $column_names)) {
                $wpdb->query("ALTER TABLE $table_name ADD COLUMN consent_text text DEFAULT NULL");
                error_log('Colonne consent_text ajoutée à la table newsletter');
            }
            
            if (!in_array('preferred_categories', $column_names)) {
                $wpdb->query("ALTER TABLE $table_name ADD COLUMN preferred_categories text DEFAULT NULL");
                error_log('Colonne preferred_categories ajoutée à la table newsletter');
            }
        }
    }

    /**
     * Ajoute un point de terminaison personnalisé pour le traitement de la newsletter
     */
    public static function add_newsletter_endpoint() {
        add_rewrite_rule('^newsletter-subscribe/?$', 'index.php?newsletter_action=subscribe', 'top');
        add_rewrite_tag('%newsletter_action%', '([^&]+)');
        
        // Ajouter la query var
        add_filter('query_vars', function($vars) {
            $vars[] = 'newsletter_action';
            return $vars;
        });
        
        // Traitement du point de terminaison
        global $wp_query;
        if (isset($wp_query->query_vars['newsletter_action']) && $wp_query->query_vars['newsletter_action'] === 'subscribe' && !is_admin()) {
            self::process_direct_subscription();
            exit;
        }
    }

    /**
     * Traitement direct de la souscription (sans passer par AJAX)
     */
    public static function process_direct_subscription() {
        // Débogage - Vérifier si la fonction est appelée
        error_log('DÉBUT process_direct_subscription - ' . date('Y-m-d H:i:s'));
        
        // Vérifier si les données POST sont reçues
        if (empty($_POST)) {
            error_log('ERREUR: Aucune donnée POST reçue dans process_direct_subscription');
            wp_redirect(home_url('/?newsletter_status=error'));
            exit;
        }
        
        // Débogage - Enregistrer les données reçues
        error_log('Traitement direct de la souscription newsletter - Données POST: ' . print_r($_POST, true));
        
        // Forcer la création de la table si elle n'existe pas
        self::create_newsletter_table();
        error_log('Table de newsletter créée ou vérifiée');
        
        // Statut par défaut (succès)
        $status = 'success';
        $message = '';
        
        try {
            // Vérification du consentement RGPD
            if (!isset($_POST['gdpr_consent'])) {
                error_log('Consentement RGPD manquant ou invalide');
                throw new Exception(__('Vous devez accepter la politique de confidentialité pour vous inscrire.', 'lejournaldesactus'));
            }
            error_log('Consentement RGPD validé');
            
            // Récupération et nettoyage des données
            $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
            $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
            error_log('Données nettoyées: email=' . $email . ', name=' . $name);
            
            // Validation des données requises
            if (empty($email) || empty($name)) {
                error_log('Données manquantes: email ou nom');
                throw new Exception(__('Veuillez remplir tous les champs obligatoires.', 'lejournaldesactus'));
            }
            
            // Validation de l'email
            if (!is_email($email)) {
                error_log('Email invalide: ' . $email);
                throw new Exception(__('Veuillez fournir une adresse email valide.', 'lejournaldesactus'));
            }
            error_log('Email validé: ' . $email);
            
            // Vérification si l'email existe déjà
            global $wpdb;
            $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
            
            $existing_subscriber = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email));
            error_log('Résultat de la requête de vérification: ' . ($existing_subscriber ? 'Email trouvé' : 'Email non trouvé'));
            
            if ($existing_subscriber) {
                if ($existing_subscriber->status === 'confirmed') {
                    error_log('L\'abonné est déjà confirmé');
                    throw new Exception(__('Vous êtes déjà inscrit à notre newsletter.', 'lejournaldesactus'));
                } elseif ($existing_subscriber->status === 'pending') {
                    error_log('L\'abonné est en attente de confirmation');
                    // Renvoyer l'email de confirmation
                    $sent = self::send_confirmation_email($email, $name, $existing_subscriber->confirmation_key);
                    if ($sent) {
                        error_log('Email de confirmation renvoyé avec succès');
                        $message = __('Un email de confirmation a été envoyé à votre adresse. Veuillez vérifier votre boîte de réception.', 'lejournaldesactus');
                        wp_redirect(home_url('/?newsletter_status=success&message=' . urlencode($message)));
                        exit;
                    } else {
                        error_log('Échec de l\'envoi de l\'email de confirmation');
                        throw new Exception(__('Une erreur est survenue lors de l\'envoi de l\'email de confirmation. Veuillez réessayer.', 'lejournaldesactus'));
                    }
                }
            }
            
            // Génération d'une clé de confirmation unique
            $confirmation_key = wp_generate_password(20, false);
            error_log('Clé de confirmation générée: ' . $confirmation_key);
            
            // Récupération du texte de consentement
            $consent_text = get_option('lejournaldesactus_privacy_text', __('J\'accepte de recevoir la newsletter et j\'ai lu et accepté la politique de confidentialité.', 'lejournaldesactus'));
            
            // Récupération de l'adresse IP
            $ip_address = $_SERVER['REMOTE_ADDR'];
            
            // Insertion dans la base de données
            error_log('Tentative d\'insertion dans la base de données');
            $result = $wpdb->insert(
                $table_name,
                array(
                    'email' => $email,
                    'name' => $name,
                    'status' => 'pending',
                    'confirmation_key' => $confirmation_key,
                    'consent_text' => $consent_text,
                    'ip_address' => $ip_address,
                    'gdpr_consent' => 1,
                    'immediate_notifications' => isset($_POST['immediate_notifications']) ? 1 : 0,
                    'preferred_categories' => isset($_POST['preferred_categories']) ? serialize($_POST['preferred_categories']) : ''
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s')
            );
            
            error_log('Résultat de l\'insertion: ' . ($result ? 'Succès' : 'Échec'));
            
            if ($result === false) {
                error_log('Erreur d\'insertion dans la base de données: ' . $wpdb->last_error);
                throw new Exception(__('Une erreur est survenue lors de l\'enregistrement de votre inscription. Veuillez réessayer.', 'lejournaldesactus'));
            }
            
            // Envoi de l'email de confirmation
            $sent = self::send_confirmation_email($email, $name, $confirmation_key);
            
            if (!$sent) {
                error_log('Échec de l\'envoi de l\'email de confirmation');
                throw new Exception(__('Une erreur est survenue lors de l\'envoi de l\'email de confirmation. Votre inscription a été enregistrée, mais vous devrez demander un nouvel email de confirmation.', 'lejournaldesactus'));
            }
            
            error_log('Email de confirmation envoyé avec succès');
            $message = __('Merci pour votre inscription ! Un email de confirmation a été envoyé à votre adresse. Veuillez vérifier votre boîte de réception.', 'lejournaldesactus');
            
        } catch (Exception $e) {
            error_log('Exception dans process_direct_subscription: ' . $e->getMessage());
            $status = 'error';
            $message = $e->getMessage();
        }
        
        error_log('FIN process_direct_subscription - Statut: ' . $status . ', Message: ' . $message);
        
        // Redirection avec le statut et le message
        wp_redirect(home_url('/?newsletter_status=' . $status . '&message=' . urlencode($message)));
        exit;
    }

    /**
     * Envoi de l'email de confirmation (double opt-in)
     */
    public static function send_confirmation_email($email, $name, $confirmation_key) {
        // Débogage
        error_log('Tentative d\'envoi d\'email de confirmation à ' . $email);
        
        $subject = sprintf(__('[%s] Confirmez votre inscription à notre newsletter', 'lejournaldesactus'), get_bloginfo('name'));
        
        $confirmation_url = add_query_arg(
            array(
                'newsletter_action' => 'confirm',
                'email' => urlencode($email),
                'key' => $confirmation_key
            ),
            get_template_directory_uri() . '/confirm-newsletter.php'
        );
        
        $message = sprintf(
            __('Bonjour %s,

Merci de vous être inscrit à la newsletter de %s.

Pour confirmer votre inscription, veuillez cliquer sur le lien suivant :
%s

Si vous n\'avez pas demandé cette inscription, vous pouvez ignorer cet email.

Cordialement,
L\'équipe de %s', 'lejournaldesactus'),
            $name,
            get_bloginfo('name'),
            $confirmation_url,
            get_bloginfo('name')
        );
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        // Débogage
        error_log('Email à envoyer: ' . $message);
        
        $sent = wp_mail($email, $subject, $message, $headers);
        
        // Débogage
        error_log('Résultat de l\'envoi: ' . ($sent ? 'Succès' : 'Échec'));
        
        return $sent;
    }

    /**
     * Confirmation de l'inscription (traitement du lien de confirmation)
     */
    public static function confirm_subscription() {
        // Débogage - Vérifier si la fonction est appelée
        error_log('DÉBUT confirm_subscription - ' . date('Y-m-d H:i:s'));
        error_log('GET params: ' . print_r($_GET, true));
        
        if (!isset($_GET['newsletter_action']) || $_GET['newsletter_action'] !== 'confirm' || !isset($_GET['email']) || !isset($_GET['key'])) {
            error_log('Paramètres manquants pour la confirmation');
            return;
        }
        
        $email = sanitize_email($_GET['email']);
        $key = sanitize_text_field($_GET['key']);
        
        error_log('Tentative de confirmation pour email=' . $email . ', key=' . $key);
        
        if (!is_email($email)) {
            error_log('Adresse email invalide: ' . $email);
            wp_die(__('Adresse email invalide.', 'lejournaldesactus'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
        
        error_log('Recherche dans la table: ' . $table_name);
        
        $subscriber = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE email = %s AND confirmation_key = %s",
            $email,
            $key
        ));
        
        error_log('Résultat de la recherche: ' . ($subscriber ? 'Abonné trouvé' : 'Abonné non trouvé'));
        
        if (!$subscriber) {
            error_log('Lien de confirmation invalide ou expiré');
            wp_die(__('Lien de confirmation invalide ou expiré.', 'lejournaldesactus'));
        }
        
        if ($subscriber->status === 'confirmed') {
            error_log('Inscription déjà confirmée');
            wp_die(__('Votre inscription a déjà été confirmée. Merci !', 'lejournaldesactus'));
        }
        
        // Mise à jour du statut
        $result = $wpdb->update(
            $table_name,
            array(
                'status' => 'confirmed',
                'confirmed_at' => current_time('mysql')
            ),
            array('email' => $email, 'confirmation_key' => $key)
        );
        
        error_log('Résultat de la mise à jour: ' . ($result ? 'Succès' : 'Échec') . ' - Erreur SQL éventuelle: ' . $wpdb->last_error);
        
        // Redirection vers une page de confirmation
        wp_redirect(add_query_arg('newsletter_confirmed', '1', home_url('/')));
        exit;
    }

    /**
     * Traitement du désabonnement
     */
    public static function unsubscribe_subscription() {
        // Cette fonction ne fait rien maintenant, car le désabonnement est géré directement par unsubscribe-newsletter.php
        return;
    }

    /**
     * Traitement de la souscription via AJAX
     */
    public static function process_subscription() {
        // Débogage
        error_log('Fonction process_subscription appelée');
        error_log('POST data: ' . print_r($_POST, true));
        
        // Vérification des données
        if (!isset($_POST['email']) || empty($_POST['email'])) {
            wp_send_json_error(array('message' => __('Veuillez fournir une adresse email valide.', 'lejournaldesactus')));
            return;
        }
        
        if (!isset($_POST['name']) || empty($_POST['name'])) {
            wp_send_json_error(array('message' => __('Veuillez fournir votre nom.', 'lejournaldesactus')));
            return;
        }
        
        $email = sanitize_email($_POST['email']);
        $name = sanitize_text_field($_POST['name']);
        
        // Vérification de l'email
        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Veuillez fournir une adresse email valide.', 'lejournaldesactus')));
            return;
        }
        
        // Vérification du consentement RGPD
        if (!isset($_POST['gdpr_consent'])) {
            wp_send_json_error(array('message' => __('Vous devez accepter les conditions pour vous inscrire.', 'lejournaldesactus')));
            return;
        }
        
        // Récupérer les préférences de notification
        $immediate_notifications = isset($_POST['immediate_notifications']) ? 1 : 0;
        
        // Récupérer les catégories préférées
        $preferred_categories = isset($_POST['preferred_categories']) && is_array($_POST['preferred_categories']) 
            ? array_map('intval', $_POST['preferred_categories']) 
            : array();
        
        // Vérification si l'email existe déjà
        global $wpdb;
        $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
        
        // Forcer la création de la table si elle n'existe pas
        self::create_newsletter_table();
        
        $existing_subscriber = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email));
        
        if ($existing_subscriber) {
            // Si déjà confirmé, renvoyer un message
            if ($existing_subscriber->status === 'confirmed') {
                wp_send_json_error(array('message' => __('Vous êtes déjà inscrit à notre newsletter.', 'lejournaldesactus')));
                return;
            }
            
            // Si en attente, renvoyer l'email de confirmation
            if ($existing_subscriber->status === 'pending') {
                $sent = self::send_confirmation_email($email, $name, $existing_subscriber->confirmation_key);
                
                if ($sent) {
                    // Mettre à jour les préférences
                    $wpdb->update(
                        $table_name,
                        array(
                            'name' => $name,
                            'immediate_notifications' => $immediate_notifications
                        ),
                        array('id' => $existing_subscriber->id)
                    );
                    
                    // Mettre à jour les catégories préférées
                    if (!empty($preferred_categories)) {
                        update_user_meta($existing_subscriber->id, 'preferred_categories', $preferred_categories);
                    }
                    
                    wp_send_json_success(array('message' => __('Un email de confirmation a été renvoyé. Veuillez vérifier votre boîte de réception.', 'lejournaldesactus')));
                } else {
                    wp_send_json_error(array('message' => __('Erreur lors de l\'envoi de l\'email de confirmation. Veuillez réessayer plus tard.', 'lejournaldesactus')));
                }
                return;
            }
            
            // Si désabonné, réactiver l'abonnement
            if ($existing_subscriber->status === 'unsubscribed') {
                // Générer une nouvelle clé de confirmation
                $confirmation_key = wp_generate_password(20, false);
                
                $updated = $wpdb->update(
                    $table_name,
                    array(
                        'name' => $name,
                        'status' => 'pending',
                        'confirmation_key' => $confirmation_key,
                        'created_at' => current_time('mysql'),
                        'confirmed_at' => null,
                        'unsubscribed_at' => null,
                        'immediate_notifications' => $immediate_notifications,
                        'gdpr_consent' => 1
                    ),
                    array('id' => $existing_subscriber->id)
                );
                
                if ($updated) {
                    // Mettre à jour les catégories préférées
                    if (!empty($preferred_categories)) {
                        update_user_meta($existing_subscriber->id, 'preferred_categories', $preferred_categories);
                    }
                    
                    $sent = self::send_confirmation_email($email, $name, $confirmation_key);
                    
                    if ($sent) {
                        wp_send_json_success(array('message' => __('Un email de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.', 'lejournaldesactus')));
                    } else {
                        wp_send_json_error(array('message' => __('Erreur lors de l\'envoi de l\'email de confirmation. Veuillez réessayer plus tard.', 'lejournaldesactus')));
                    }
                } else {
                    wp_send_json_error(array('message' => __('Une erreur est survenue lors de l\'enregistrement de votre inscription. Veuillez réessayer.', 'lejournaldesactus')));
                }
                return;
            }
        }
        
        // Nouveau souscripteur
        // Générer une clé de confirmation
        $confirmation_key = wp_generate_password(20, false);
        
        // Récupération du texte de consentement
        $consent_text = get_option('lejournaldesactus_privacy_text', __('J\'accepte de recevoir la newsletter et j\'ai lu et accepté la politique de confidentialité.', 'lejournaldesactus'));
        
        // Récupération de l'adresse IP
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        // Insérer le nouveau souscripteur
        $result = $wpdb->insert(
            $table_name,
            array(
                'email' => $email,
                'name' => $name,
                'status' => 'pending',
                'confirmation_key' => $confirmation_key,
                'created_at' => current_time('mysql'),
                'ip_address' => $ip_address,
                'consent_text' => $consent_text,
                'immediate_notifications' => $immediate_notifications,
                'gdpr_consent' => 1,
                'preferred_categories' => !empty($preferred_categories) ? serialize($preferred_categories) : ''
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s')
        );
        
        if ($result === false) {
            error_log('Erreur d\'insertion dans la base de données: ' . $wpdb->last_error);
            wp_send_json_error(array('message' => __('Une erreur est survenue lors de l\'enregistrement de votre inscription. Veuillez réessayer.', 'lejournaldesactus')));
            return;
        }
        
        // Envoi de l'email de confirmation
        $sent = self::send_confirmation_email($email, $name, $confirmation_key);
        
        if ($sent) {
            wp_send_json_success(array('message' => __('Merci pour votre inscription ! Un email de confirmation a été envoyé à votre adresse. Veuillez vérifier votre boîte de réception.', 'lejournaldesactus')));
        } else {
            wp_send_json_error(array('message' => __('Une erreur est survenue lors de l\'envoi de l\'email de confirmation. Votre inscription a été enregistrée, mais vous devrez demander un nouvel email de confirmation.', 'lejournaldesactus')));
        }
    }

    /**
     * Shortcode pour afficher le formulaire d'inscription
     */
    public static function newsletter_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'title' => __('Abonnez-vous à notre newsletter', 'lejournaldesactus'),
                'description' => __('Restez informé de nos derniers articles et actualités.', 'lejournaldesactus')
            ),
            $atts,
            'lejournaldesactus_newsletter'
        );
        
        // Forcer la création de la table si elle n'existe pas
        self::create_newsletter_table();
        
        // Récupérer l'URL de traitement direct
        $process_url = home_url('/process-newsletter.php');
        
        ob_start();
        ?>
        <div class="lejournaldesactus-newsletter-form">
            <h3><?php echo esc_html($atts['title']); ?></h3>
            <p><?php echo esc_html($atts['description']); ?></p>
            
            <form action="<?php echo esc_url($process_url); ?>" method="post" class="newsletter-form">
                <div class="form-group">
                    <input type="text" name="name" placeholder="<?php esc_attr_e('Votre nom', 'lejournaldesactus'); ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="<?php esc_attr_e('Votre email', 'lejournaldesactus'); ?>" required>
                </div>
                <div class="form-group consent-group">
                    <label>
                        <input type="checkbox" name="gdpr_consent" value="yes" checked>
                        <?php echo wp_kses_post(get_option('lejournaldesactus_privacy_text', __('J\'accepte de recevoir la newsletter et j\'ai lu et accepté la <a href="/politique-de-confidentialite">politique de confidentialité</a>.', 'lejournaldesactus'))); ?>
                    </label>
                </div>
                <div class="form-group">
                    <button type="submit"><?php esc_html_e('S\'abonner', 'lejournaldesactus'); ?></button>
                </div>
                <div class="newsletter-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Ajouter une page dans l'administration
     */
    public static function add_admin_menu() {
        add_menu_page(
            __('Gestion de la Newsletter', 'lejournaldesactus'),
            __('Newsletter', 'lejournaldesactus'),
            'manage_options',
            'lejournaldesactus-newsletter',
            array(__CLASS__, 'admin_page'),
            'dashicons-email',
            30
        );
        
        add_submenu_page(
            'lejournaldesactus-newsletter',
            __('Abonnés', 'lejournaldesactus'),
            __('Abonnés', 'lejournaldesactus'),
            'manage_options',
            'lejournaldesactus-newsletter',
            array(__CLASS__, 'admin_page')
        );
        
        add_submenu_page(
            'lejournaldesactus-newsletter',
            __('Paramètres', 'lejournaldesactus'),
            __('Paramètres', 'lejournaldesactus'),
            'manage_options',
            'lejournaldesactus-newsletter-settings',
            array(__CLASS__, 'settings_page')
        );
    }

    /**
     * Page d'administration pour la gestion des abonnés
     */
    public static function admin_page() {
        // Traitement des actions individuelles
        if (isset($_GET['action']) && isset($_GET['subscriber_id']) && is_numeric($_GET['subscriber_id'])) {
            $action = sanitize_text_field($_GET['action']);
            $subscriber_id = intval($_GET['subscriber_id']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
            
            // Vérifier que l'abonné existe
            $subscriber = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $subscriber_id));
            
            if ($subscriber) {
                switch ($action) {
                    case 'confirm':
                        $wpdb->update(
                            $table_name,
                            array(
                                'status' => 'confirmed',
                                'confirmed_at' => current_time('mysql')
                            ),
                            array('id' => $subscriber_id)
                        );
                        echo '<div class="notice notice-success"><p>' . __('Abonné confirmé avec succès.', 'lejournaldesactus') . '</p></div>';
                        break;
                        
                    case 'unsubscribe':
                        $wpdb->update(
                            $table_name,
                            array(
                                'status' => 'unsubscribed',
                                'unsubscribed_at' => current_time('mysql')
                            ),
                            array('id' => $subscriber_id)
                        );
                        echo '<div class="notice notice-success"><p>' . __('Abonné désabonné avec succès.', 'lejournaldesactus') . '</p></div>';
                        break;
                        
                    case 'delete':
                        $wpdb->delete($table_name, array('id' => $subscriber_id));
                        echo '<div class="notice notice-success"><p>' . __('Abonné supprimé avec succès.', 'lejournaldesactus') . '</p></div>';
                        break;
                        
                    case 'resend':
                        // Renvoyer l'email de confirmation
                        if ($subscriber->status === 'pending') {
                            $sent = self::send_confirmation_email($subscriber->email, $subscriber->name, $subscriber->confirmation_key);
                            if ($sent) {
                                echo '<div class="notice notice-success"><p>' . __('Email de confirmation renvoyé avec succès.', 'lejournaldesactus') . '</p></div>';
                            } else {
                                echo '<div class="notice notice-error"><p>' . __('Erreur lors de l\'envoi de l\'email de confirmation.', 'lejournaldesactus') . '</p></div>';
                            }
                        }
                        break;
                }
            }
            
            // Rediriger pour éviter de répéter l'action lors d'un rafraîchissement
            $redirect_url = remove_query_arg(array('action', 'subscriber_id'), $_SERVER['REQUEST_URI']);
            echo '<script>window.history.replaceState({}, document.title, "' . esc_js($redirect_url) . '");</script>';
        }
        
        // Traitement des actions en masse
        if (isset($_POST['newsletter_action']) && isset($_POST['subscriber_ids']) && is_array($_POST['subscriber_ids'])) {
            $action = sanitize_text_field($_POST['newsletter_action']);
            $subscriber_ids = array_map('intval', $_POST['subscriber_ids']);
            
            if (!empty($subscriber_ids)) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
                
                switch ($action) {
                    case 'delete':
                        foreach ($subscriber_ids as $id) {
                            $wpdb->delete($table_name, array('id' => $id));
                        }
                        echo '<div class="notice notice-success"><p>' . __('Abonnés supprimés avec succès.', 'lejournaldesactus') . '</p></div>';
                        break;
                        
                    case 'confirm':
                        foreach ($subscriber_ids as $id) {
                            $wpdb->update(
                                $table_name,
                                array(
                                    'status' => 'confirmed',
                                    'confirmed_at' => current_time('mysql')
                                ),
                                array('id' => $id)
                            );
                        }
                        echo '<div class="notice notice-success"><p>' . __('Abonnés confirmés avec succès.', 'lejournaldesactus') . '</p></div>';
                        break;
                        
                    case 'unsubscribe':
                        foreach ($subscriber_ids as $id) {
                            $wpdb->update(
                                $table_name,
                                array(
                                    'status' => 'unsubscribed',
                                    'unsubscribed_at' => current_time('mysql')
                                ),
                                array('id' => $id)
                            );
                        }
                        echo '<div class="notice notice-success"><p>' . __('Abonnés désabonnés avec succès.', 'lejournaldesactus') . '</p></div>';
                        break;
                }
            }
        }
        
        // Récupération des abonnés
        global $wpdb;
        $table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';
        
        // Filtrage par statut
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
        $where_clause = '';
        
        if ($status_filter !== 'all') {
            $where_clause = $wpdb->prepare("WHERE status = %s", $status_filter);
        }
        
        $subscribers = $wpdb->get_results("SELECT * FROM $table_name $where_clause ORDER BY created_at DESC");
        
        // Comptage des abonnés par statut
        $count_confirmed = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'confirmed'");
        $count_pending = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
        $count_unsubscribed = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'unsubscribed'");
        $count_all = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        // Affichage de la page
        ?>
        <div class="wrap">
            <h1><?php _e('Gestion des abonnés à la newsletter', 'lejournaldesactus'); ?></h1>
            
            <ul class="subsubsub">
                <li><a href="?page=lejournaldesactus-newsletter&status=all" <?php echo $status_filter === 'all' ? 'class="current"' : ''; ?>><?php _e('Tous', 'lejournaldesactus'); ?> <span class="count">(<?php echo $count_all; ?>)</span></a> |</li>
                <li><a href="?page=lejournaldesactus-newsletter&status=confirmed" <?php echo $status_filter === 'confirmed' ? 'class="current"' : ''; ?>><?php _e('Confirmés', 'lejournaldesactus'); ?> <span class="count">(<?php echo $count_confirmed; ?>)</span></a> |</li>
                <li><a href="?page=lejournaldesactus-newsletter&status=pending" <?php echo $status_filter === 'pending' ? 'class="current"' : ''; ?>><?php _e('En attente', 'lejournaldesactus'); ?> <span class="count">(<?php echo $count_pending; ?>)</span></a> |</li>
                <li><a href="?page=lejournaldesactus-newsletter&status=unsubscribed" <?php echo $status_filter === 'unsubscribed' ? 'class="current"' : ''; ?>><?php _e('Désabonnés', 'lejournaldesactus'); ?> <span class="count">(<?php echo $count_unsubscribed; ?>)</span></a></li>
            </ul>
            
            <form method="post">
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <select name="newsletter_action">
                            <option value="-1"><?php _e('Actions groupées', 'lejournaldesactus'); ?></option>
                            <option value="delete"><?php _e('Supprimer', 'lejournaldesactus'); ?></option>
                            <option value="confirm"><?php _e('Marquer comme confirmé', 'lejournaldesactus'); ?></option>
                            <option value="unsubscribe"><?php _e('Désabonner', 'lejournaldesactus'); ?></option>
                        </select>
                        <input type="submit" class="button action" value="<?php esc_attr_e('Appliquer', 'lejournaldesactus'); ?>">
                    </div>
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo sprintf(_n('%s abonné', '%s abonnés', count($subscribers), 'lejournaldesactus'), count($subscribers)); ?></span>
                    </div>
                </div>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td id="cb" class="manage-column column-cb check-column">
                                <input type="checkbox" id="cb-select-all-1">
                            </td>
                            <th scope="col" class="manage-column column-name"><?php _e('Nom', 'lejournaldesactus'); ?></th>
                            <th scope="col" class="manage-column column-email"><?php _e('Email', 'lejournaldesactus'); ?></th>
                            <th scope="col" class="manage-column column-status"><?php _e('Statut', 'lejournaldesactus'); ?></th>
                            <th scope="col" class="manage-column column-date"><?php _e('Date d\'inscription', 'lejournaldesactus'); ?></th>
                            <th scope="col" class="manage-column column-date"><?php _e('Date de confirmation', 'lejournaldesactus'); ?></th>
                            <th scope="col" class="manage-column column-actions"><?php _e('Actions', 'lejournaldesactus'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subscribers)) : ?>
                            <tr>
                                <td colspan="6"><?php _e('Aucun abonné trouvé.', 'lejournaldesactus'); ?></td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($subscribers as $subscriber) : ?>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="subscriber_ids[]" value="<?php echo $subscriber->id; ?>">
                                    </th>
                                    <td class="column-name"><?php echo esc_html($subscriber->name); ?></td>
                                    <td class="column-email"><?php echo esc_html($subscriber->email); ?></td>
                                    <td class="column-status">
                                        <?php
                                        switch ($subscriber->status) {
                                            case 'confirmed':
                                                echo '<span class="status-confirmed">' . __('Confirmé', 'lejournaldesactus') . '</span>';
                                                break;
                                            case 'pending':
                                                echo '<span class="status-pending">' . __('En attente', 'lejournaldesactus') . '</span>';
                                                break;
                                            case 'unsubscribed':
                                                echo '<span class="status-unsubscribed">' . __('Désabonné', 'lejournaldesactus') . '</span>';
                                                break;
                                            default:
                                                echo esc_html($subscriber->status);
                                        }
                                        ?>
                                    </td>
                                    <td class="column-date"><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($subscriber->created_at)); ?></td>
                                    <td class="column-date">
                                        <?php
                                        if ($subscriber->confirmed_at) {
                                            echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($subscriber->confirmed_at));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td class="column-actions">
                                        <?php if ($subscriber->status === 'pending') : ?>
                                            <a href="?page=lejournaldesactus-newsletter&action=confirm&subscriber_id=<?php echo $subscriber->id; ?>" class="button button-small"><?php _e('Confirmer', 'lejournaldesactus'); ?></a>
                                            <a href="?page=lejournaldesactus-newsletter&action=resend&subscriber_id=<?php echo $subscriber->id; ?>" class="button button-small"><?php _e('Renvoyer l\'email', 'lejournaldesactus'); ?></a>
                                        <?php elseif ($subscriber->status === 'confirmed') : ?>
                                            <a href="?page=lejournaldesactus-newsletter&action=unsubscribe&subscriber_id=<?php echo $subscriber->id; ?>" class="button button-small"><?php _e('Désabonner', 'lejournaldesactus'); ?></a>
                                        <?php elseif ($subscriber->status === 'unsubscribed') : ?>
                                            <a href="?page=lejournaldesactus-newsletter&action=confirm&subscriber_id=<?php echo $subscriber->id; ?>" class="button button-small"><?php _e('Réabonner', 'lejournaldesactus'); ?></a>
                                        <?php endif; ?>
                                        <a href="?page=lejournaldesactus-newsletter&action=delete&subscriber_id=<?php echo $subscriber->id; ?>" class="button button-small" onclick="return confirm('<?php esc_attr_e('Êtes-vous sûr de vouloir supprimer cet abonné ?', 'lejournaldesactus'); ?>')"><?php _e('Supprimer', 'lejournaldesactus'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
            
            <style>
                .status-confirmed {
                    color: green;
                    font-weight: bold;
                }
                .status-pending {
                    color: orange;
                    font-weight: bold;
                }
                .status-unsubscribed {
                    color: red;
                    font-weight: bold;
                }
                .column-actions .button {
                    margin-right: 5px;
                    margin-bottom: 5px;
                }
            </style>
        </div>
        <?php
    }

    /**
     * Page de paramètres de la newsletter
     */
    public static function settings_page() {
        // Traitement du formulaire
        if (isset($_POST['lejournaldesactus_newsletter_settings_nonce']) && wp_verify_nonce($_POST['lejournaldesactus_newsletter_settings_nonce'], 'lejournaldesactus_newsletter_settings')) {
            // Mise à jour des options
            update_option('lejournaldesactus_privacy_text', stripslashes(wp_kses_post($_POST['privacy_text'])));
            update_option('lejournaldesactus_newsletter_sender_name', sanitize_text_field($_POST['newsletter_sender_name']));
            update_option('lejournaldesactus_newsletter_sender_email', sanitize_email($_POST['newsletter_sender_email']));
            update_option('lejournaldesactus_newsletter_weekly_day', sanitize_text_field($_POST['newsletter_weekly_day']));
            
            echo '<div class="notice notice-success"><p>' . __('Paramètres enregistrés avec succès.', 'lejournaldesactus') . '</p></div>';
        }
        
        // Récupération des options
        $privacy_text = stripslashes(get_option('lejournaldesactus_privacy_text', __('J\'accepte de recevoir la newsletter et j\'ai lu et accepté la politique de confidentialité.', 'lejournaldesactus')));
        $sender_name = get_option('lejournaldesactus_newsletter_sender_name', get_bloginfo('name'));
        $sender_email = get_option('lejournaldesactus_newsletter_sender_email', get_option('admin_email'));
        $weekly_day = get_option('lejournaldesactus_newsletter_weekly_day', 'monday');
        
        // Affichage de la page
        ?>
        <div class="wrap">
            <h1><?php _e('Paramètres de la newsletter', 'lejournaldesactus'); ?></h1>
            
            <form method="post">
                <h2><?php _e('Paramètres généraux', 'lejournaldesactus'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Nom de l\'expéditeur', 'lejournaldesactus'); ?></th>
                        <td>
                            <input type="text" name="newsletter_sender_name" value="<?php echo esc_attr($sender_name); ?>" class="regular-text">
                            <p class="description"><?php _e('Nom qui apparaîtra comme expéditeur des emails de newsletter.', 'lejournaldesactus'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Email de l\'expéditeur', 'lejournaldesactus'); ?></th>
                        <td>
                            <input type="email" name="newsletter_sender_email" value="<?php echo esc_attr($sender_email); ?>" class="regular-text">
                            <p class="description"><?php _e('Adresse email qui sera utilisée pour envoyer les newsletters.', 'lejournaldesactus'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Jour d\'envoi hebdomadaire', 'lejournaldesactus'); ?></th>
                        <td>
                            <select name="newsletter_weekly_day">
                                <option value="monday" <?php selected($weekly_day, 'monday'); ?>><?php _e('Lundi', 'lejournaldesactus'); ?></option>
                                <option value="tuesday" <?php selected($weekly_day, 'tuesday'); ?>><?php _e('Mardi', 'lejournaldesactus'); ?></option>
                                <option value="wednesday" <?php selected($weekly_day, 'wednesday'); ?>><?php _e('Mercredi', 'lejournaldesactus'); ?></option>
                                <option value="thursday" <?php selected($weekly_day, 'thursday'); ?>><?php _e('Jeudi', 'lejournaldesactus'); ?></option>
                                <option value="friday" <?php selected($weekly_day, 'friday'); ?>><?php _e('Vendredi', 'lejournaldesactus'); ?></option>
                                <option value="saturday" <?php selected($weekly_day, 'saturday'); ?>><?php _e('Samedi', 'lejournaldesactus'); ?></option>
                                <option value="sunday" <?php selected($weekly_day, 'sunday'); ?>><?php _e('Dimanche', 'lejournaldesactus'); ?></option>
                            </select>
                            <p class="description"><?php _e('Jour de la semaine où la newsletter hebdomadaire sera envoyée.', 'lejournaldesactus'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php _e('Paramètres RGPD', 'lejournaldesactus'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Texte de consentement RGPD', 'lejournaldesactus'); ?></th>
                        <td>
                            <?php
                            wp_editor(
                                $privacy_text,
                                'privacy_text',
                                array(
                                    'textarea_name' => 'privacy_text',
                                    'textarea_rows' => 5,
                                    'media_buttons' => false,
                                    'teeny' => true
                                )
                            );
                            ?>
                            <p class="description"><?php _e('Ce texte sera affiché à côté de la case à cocher de consentement RGPD dans le formulaire d\'inscription. Vous pouvez utiliser du HTML pour ajouter des liens, par exemple vers votre politique de confidentialité.', 'lejournaldesactus'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php wp_nonce_field('lejournaldesactus_newsletter_settings', 'lejournaldesactus_newsletter_settings_nonce'); ?>
                <p class="submit">
                    <input type="submit" class="button button-primary" value="<?php esc_attr_e('Enregistrer les paramètres', 'lejournaldesactus'); ?>">
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * Flush les règles de réécriture
     */
    public static function flush_rewrite_rules() {
        self::add_newsletter_endpoint();
        flush_rewrite_rules();
    }

    /**
     * Force la mise à jour des règles de réécriture
     * Cette fonction peut être appelée manuellement pour mettre à jour les règles
     */
    public static function force_flush_rewrite_rules() {
        self::add_newsletter_endpoint();
        flush_rewrite_rules();
        return true;
    }
}

// Initialisation de la classe
LeJournalDesActus_Newsletter::init();
