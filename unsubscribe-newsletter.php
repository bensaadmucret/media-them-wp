<?php
/**
 * Fichier de désabonnement à la newsletter
 */

// Définir le chemin vers le fichier wp-load.php
$wp_load_paths = array(
    // Chemin direct
    dirname(__FILE__) . '/../../../wp-load.php',
    // Chemin absolu via DOCUMENT_ROOT
    $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php',
    // Chemin relatif
    '../../../wp-load.php',
    // Chemin pour Local by Flywheel
    dirname(__FILE__) . '/../../../../wp-load.php'
);

// Essayer chaque chemin possible
$wp_loaded = false;
foreach ($wp_load_paths as $wp_load_path) {
    if (file_exists($wp_load_path)) {
        require_once($wp_load_path);
        $wp_loaded = true;
        break;
    }
}

// Si aucun chemin ne fonctionne, afficher une erreur
if (!$wp_loaded) {
    die('Impossible de charger WordPress. Veuillez contacter l\'administrateur du site.');
}

// Débogage
error_log('unsubscribe-newsletter.php - WordPress chargé avec succès');

// Inclure la classe newsletter si nécessaire
require_once('inc/newsletter.php');

// Récupérer les paramètres
$newsletter_action = isset($_GET['newsletter_action']) ? sanitize_text_field($_GET['newsletter_action']) : '';
$email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
$key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

// Débogage
error_log('unsubscribe-newsletter.php appelé avec action=' . $newsletter_action . ', email=' . $email . ', key=' . $key);

// Vérifier les paramètres
if ($newsletter_action !== 'unsubscribe' || empty($email) || empty($key)) {
    wp_die(__('Lien de désabonnement invalide. Veuillez vérifier l\'URL ou contacter l\'administrateur.', 'lejournaldesactus'));
}

// Vérifier l'email
if (!is_email($email)) {
    wp_die(__('Adresse email invalide.', 'lejournaldesactus'));
}

// Vérifier dans la base de données
global $wpdb;
$table_name = $wpdb->prefix . 'lejournaldesactus_newsletter';

$subscriber = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_name WHERE email = %s AND confirmation_key = %s",
    $email,
    $key
));

// Débogage
error_log('Résultat de la recherche: ' . ($subscriber ? 'Abonné trouvé' : 'Abonné non trouvé'));

if (!$subscriber) {
    wp_die(__('Lien de désabonnement invalide ou expiré.', 'lejournaldesactus'));
}

if ($subscriber->status === 'unsubscribed') {
    wp_die(__('Vous êtes déjà désabonné de notre newsletter.', 'lejournaldesactus'));
}

// Mise à jour du statut
$result = $wpdb->update(
    $table_name,
    array(
        'status' => 'unsubscribed',
        'unsubscribed_at' => current_time('mysql')
    ),
    array(
        'email' => $email,
        'confirmation_key' => $key
    )
);

// Débogage
error_log('Résultat de la mise à jour: ' . ($result ? 'Succès' : 'Échec') . ' - Erreur SQL éventuelle: ' . $wpdb->last_error);

// Afficher un message de confirmation
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('Désabonnement', 'lejournaldesactus'); ?> - <?php bloginfo('name'); ?></title>
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        .confirmation-message {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .resubscribe {
            margin-top: 20px;
            font-size: 16px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="confirmation-message">
        <h1><?php _e('Désabonnement confirmé', 'lejournaldesactus'); ?></h1>
        <p><?php _e('Vous avez été désabonné de notre newsletter avec succès.', 'lejournaldesactus'); ?></p>
        <p><?php _e('Nous sommes désolés de vous voir partir. Vous ne recevrez plus d\'emails de notre part.', 'lejournaldesactus'); ?></p>
        <p><a href="<?php echo esc_url(home_url('/')); ?>" class="btn"><?php _e('Retour à l\'accueil', 'lejournaldesactus'); ?></a></p>
        
        <div class="resubscribe">
            <p><?php _e('Vous avez changé d\'avis ?', 'lejournaldesactus'); ?></p>
            <p><a href="<?php echo esc_url(home_url('/')); ?>#newsletter-form"><?php _e('Réabonnez-vous à notre newsletter', 'lejournaldesactus'); ?></a></p>
        </div>
    </div>
</body>
</html>
<?php
exit;
?>
