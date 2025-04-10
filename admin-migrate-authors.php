<?php
/**
 * Script d'administration pour migrer les auteurs
 * À exécuter une seule fois puis supprimer
 * 
 * @package LeJournalDesActus
 */

// Vérifier que le script est exécuté dans WordPress
if (!defined('ABSPATH')) {
    define('WP_USE_THEMES', false);
    require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
}

// Vérifier que l'utilisateur est administrateur
if (!current_user_can('manage_options')) {
    wp_die('Accès refusé');
}

// Enqueue le fichier CSS
function enqueue_migration_styles() {
    wp_enqueue_style('admin-migrate', get_template_directory_uri() . '/assets/css/admin-migrate.css', array(), '1.0.0');
}
enqueue_migration_styles();

// Vérifier le nonce pour la sécurité
$nonce_action = 'migrate_authors_nonce_action';
$nonce_name = 'migrate_authors_nonce';
$is_form_submitted = isset($_POST[$nonce_name]) && wp_verify_nonce($_POST[$nonce_name], $nonce_action);

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_html_e('Migration des auteurs', 'lejournaldesactus'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="migrate-container">
        <h1 class="migrate-title"><?php esc_html_e('Migration des auteurs', 'lejournaldesactus'); ?></h1>

        <?php if (!$is_form_submitted) : ?>
            <p><?php esc_html_e('Ce script va migrer tous les profils d\'auteur du type', 'lejournaldesactus'); ?> <code>custom_author</code> <?php esc_html_e('vers le type', 'lejournaldesactus'); ?> <code>author</code>.</p>
            <p><strong><?php esc_html_e('Attention :', 'lejournaldesactus'); ?></strong> <?php esc_html_e('Cette opération est irréversible. Assurez-vous d\'avoir une sauvegarde de votre base de données avant de continuer.', 'lejournaldesactus'); ?></p>
            
            <form method="post" action="">
                <?php wp_nonce_field($nonce_action, $nonce_name); ?>
                <button type="submit" class="migrate-button"><?php esc_html_e('Lancer la migration', 'lejournaldesactus'); ?></button>
            </form>
            
        <?php else : ?>
            <?php
            // Étape 1: Mettre à jour les règles de réécriture
            flush_rewrite_rules(true);
            ?>
            <div class="migrate-success">
                <strong>✓</strong> <?php esc_html_e('Règles de réécriture mises à jour', 'lejournaldesactus'); ?>
            </div>

            <?php
            // Étape 2: Migrer tous les auteurs
            $args = array(
                'post_type' => 'custom_author',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $custom_authors = get_posts($args);
            $migrated_count = 0;
            $skipped_count = 0;
            ?>

            <h2><?php esc_html_e('Résultats de la migration', 'lejournaldesactus'); ?></h2>
            <table class="migrate-table">
                <tr>
                    <th><?php esc_html_e('ID', 'lejournaldesactus'); ?></th>
                    <th><?php esc_html_e('Titre', 'lejournaldesactus'); ?></th>
                    <th><?php esc_html_e('Slug', 'lejournaldesactus'); ?></th>
                    <th><?php esc_html_e('Statut', 'lejournaldesactus'); ?></th>
                    <th><?php esc_html_e('Nouvel ID', 'lejournaldesactus'); ?></th>
                </tr>

                <?php if (!empty($custom_authors)) : ?>
                    <?php foreach ($custom_authors as $author_post) : ?>
                        <?php
                        // Vérifier si un author avec le même slug existe déjà
                        $existing = get_posts(array(
                            'name' => $author_post->post_name,
                            'post_type' => 'author',
                            'post_status' => 'publish',
                            'numberposts' => 1
                        ));
                        
                        if (empty($existing)) :
                            // Créer un nouveau post de type 'author'
                            $author_args = array(
                                'post_title' => $author_post->post_title,
                                'post_content' => $author_post->post_content,
                                'post_name' => $author_post->post_name,
                                'post_type' => 'author',
                                'post_status' => 'publish'
                            );
                            
                            // Insérer le post
                            $author_id = wp_insert_post($author_args);
                            
                            if (!is_wp_error($author_id)) :
                                // Copier toutes les métadonnées
                                $meta_keys = get_post_custom_keys($author_post->ID);
                                if ($meta_keys) {
                                    foreach ($meta_keys as $key) {
                                        $meta_values = get_post_custom_values($key, $author_post->ID);
                                        foreach ($meta_values as $value) {
                                            add_post_meta($author_id, $key, maybe_unserialize($value));
                                        }
                                    }
                                }
                                
                                // Copier l'image mise en avant si elle existe
                                if (has_post_thumbnail($author_post->ID)) {
                                    $thumbnail_id = get_post_thumbnail_id($author_post->ID);
                                    set_post_thumbnail($author_id, $thumbnail_id);
                                }
                                
                                $migrated_count++;
                                ?>
                                <tr>
                                    <td><?php echo $author_post->ID; ?></td>
                                    <td><?php echo esc_html($author_post->post_title); ?></td>
                                    <td><?php echo $author_post->post_name; ?></td>
                                    <td><span class="migrate-status-success">✓ <?php esc_html_e('Migré', 'lejournaldesactus'); ?></span></td>
                                    <td><?php echo $author_id; ?></td>
                                </tr>
                                <?php
                            else :
                                ?>
                                <tr>
                                    <td><?php echo $author_post->ID; ?></td>
                                    <td><?php echo esc_html($author_post->post_title); ?></td>
                                    <td><?php echo $author_post->post_name; ?></td>
                                    <td><span class="migrate-status-error">✗ <?php esc_html_e('Erreur', 'lejournaldesactus'); ?></span></td>
                                    <td><?php echo $author_id->get_error_message(); ?></td>
                                </tr>
                                <?php
                            endif;
                        else :
                            $skipped_count++;
                            ?>
                            <tr>
                                <td><?php echo $author_post->ID; ?></td>
                                <td><?php echo esc_html($author_post->post_title); ?></td>
                                <td><?php echo $author_post->post_name; ?></td>
                                <td><span class="migrate-status-warning">⚠ <?php esc_html_e('Ignoré (existe déjà)', 'lejournaldesactus'); ?></span></td>
                                <td><?php echo $existing[0]->ID; ?></td>
                            </tr>
                            <?php
                        endif;
                    endforeach;
                else :
                    ?>
                    <tr>
                        <td colspan="5" style="text-align: center;"><?php esc_html_e('Aucun auteur personnalisé trouvé', 'lejournaldesactus'); ?></td>
                    </tr>
                    <?php
                endif;
                ?>
            </table>

            <div class="migrate-summary">
                <h3><?php esc_html_e('Résumé de la migration', 'lejournaldesactus'); ?></h3>
                <ul>
                    <li><strong><?php esc_html_e('Total d\'auteurs traités:', 'lejournaldesactus'); ?></strong> <?php echo count($custom_authors); ?></li>
                    <li><strong><?php esc_html_e('Auteurs migrés avec succès:', 'lejournaldesactus'); ?></strong> <?php echo $migrated_count; ?></li>
                    <li><strong><?php esc_html_e('Auteurs ignorés (déjà existants):', 'lejournaldesactus'); ?></strong> <?php echo $skipped_count; ?></li>
                </ul>
            </div>

            <?php
            // Marquer comme fait
            update_option('lejournaldesactus_author_rewrite_flushed', '1');
            update_option('lejournaldesactus_authors_migrated', '1');
            ?>

            <div class="migrate-next-steps">
                <h3><?php esc_html_e('Prochaines étapes', 'lejournaldesactus'); ?></h3>
                <ol>
                    <li><?php esc_html_e('Vérifiez que les profils d\'auteur sont accessibles avec les nouvelles URL (format:', 'lejournaldesactus'); ?> <code>/auteur/nom-de-lauteur/</code>)</li>
                    <li><?php esc_html_e('Si tout fonctionne correctement, vous pouvez supprimer le fichier', 'lejournaldesactus'); ?> <code>single-custom_author.php</code></li>
                    <li><?php esc_html_e('Supprimez ce script d\'administration après utilisation', 'lejournaldesactus'); ?></li>
                </ol>
            </div>

            <p><a href="<?php echo admin_url(); ?>" class="migrate-button"><?php esc_html_e('Retour à l\'administration', 'lejournaldesactus'); ?></a></p>
        <?php endif; ?>
    </div>
    <?php wp_footer(); ?>
</body>
</html>
