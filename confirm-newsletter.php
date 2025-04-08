<?php
/**
 * Fichier de confirmation des abonnements à la newsletter
 */

// Charger WordPress
require_once('../../../wp-load.php');

// Inclure la classe newsletter si nécessaire
require_once('inc/newsletter.php');

// Récupérer les paramètres
$newsletter_action = isset($_GET['newsletter_action']) ? sanitize_text_field($_GET['newsletter_action']) : '';
$email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
$key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

// Débogage
error_log('confirm-newsletter.php appelé avec action=' . $newsletter_action . ', email=' . $email . ', key=' . $key);

// Vérifier les paramètres
if ($newsletter_action !== 'confirm' || empty($email) || empty($key)) {
    wp_die(__('Lien de confirmation invalide. Veuillez vérifier l\'URL ou contacter l\'administrateur.', 'lejournaldesactus'));
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
    wp_die(__('Lien de confirmation invalide ou expiré.', 'lejournaldesactus'));
}

if ($subscriber->status === 'confirmed') {
    wp_die(__('Votre inscription a déjà été confirmée. Merci !', 'lejournaldesactus'));
}

// Mise à jour du statut
$result = $wpdb->update(
    $table_name,
    array(
        'status' => 'confirmed',
        'confirmed_at' => current_time('mysql')
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
    <title><?php _e('Confirmation d\'inscription', 'lejournaldesactus'); ?> - <?php bloginfo('name'); ?></title>
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
            color: #28a745;
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
    </style>
</head>
<body>
    <div class="confirmation-message">
        <h1><?php _e('Inscription confirmée !', 'lejournaldesactus'); ?></h1>
        <p><?php _e('Merci d\'avoir confirmé votre inscription à notre newsletter.', 'lejournaldesactus'); ?></p>
        <p><?php _e('Vous recevrez désormais nos dernières actualités directement dans votre boîte mail.', 'lejournaldesactus'); ?></p>
        <p><a href="<?php echo esc_url(home_url('/')); ?>" class="btn"><?php _e('Retour à l\'accueil', 'lejournaldesactus'); ?></a></p>
    </div>
</body>
</html>
<?php
exit;
?>
