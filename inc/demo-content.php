<?php
/**
 * Générateur de contenu de démonstration
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe pour générer du contenu de démonstration
 */
class Lejournaldesactus_Demo_Content {
    
    // Catégories à créer
    private $categories = array(
        'actualites' => 'Actualités générales',
        'tech' => 'Technologie',
        'sport' => 'Sport',
        'sante' => 'Santé',
        'culture' => 'Culture',
        'economie' => 'Économie',
        'environnement' => 'Environnement',
        'politique' => 'Politique'
    );
    
    // Auteurs à créer
    private $authors = array(
        array(
            'name' => 'Sophie Martin',
            'role' => 'editor',
            'bio' => 'Journaliste spécialisée en actualités générales et politique avec plus de 10 ans d\'expérience.',
            'avatar' => 'woman-1.jpg'
        ),
        array(
            'name' => 'Thomas Dubois',
            'role' => 'author',
            'bio' => 'Expert en technologie et innovation, passionné par les nouvelles tendances numériques.',
            'avatar' => 'man-1.jpg'
        ),
        array(
            'name' => 'Émilie Lefèvre',
            'role' => 'author',
            'bio' => 'Spécialiste en santé et bien-être, diplômée en nutrition et sciences du sport.',
            'avatar' => 'woman-2.jpg'
        ),
        array(
            'name' => 'Alexandre Moreau',
            'role' => 'author',
            'bio' => 'Journaliste sportif, couvrant les grands événements internationaux depuis 2015.',
            'avatar' => 'man-2.jpg'
        ),
        array(
            'name' => 'Julie Petit',
            'role' => 'contributor',
            'bio' => 'Passionnée de culture et d\'art, chroniqueuse et critique culturelle.',
            'avatar' => 'woman-3.jpg'
        )
    );
    
    /**
     * Constructeur
     */
    public function __construct() {
        // Ajouter une page d'administration
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Ajouter les hooks pour traiter les actions
        add_action('admin_post_generate_demo_content', array($this, 'generate_demo_content'));
    }
    
    /**
     * Ajouter une page d'administration
     */
    public function add_admin_menu() {
        add_submenu_page(
            'themes.php',
            __('Générateur de contenu', 'lejournaldesactus'),
            __('Générateur de contenu', 'lejournaldesactus'),
            'manage_options',
            'lejournaldesactus-demo-content',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Afficher la page d'administration
     */
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Générateur de contenu de démonstration', 'lejournaldesactus'); ?></h1>
            
            <div class="notice notice-warning">
                <p><strong><?php _e('Attention :', 'lejournaldesactus'); ?></strong> <?php _e('Cette action va générer du contenu de démonstration sur votre site. Il est recommandé de faire une sauvegarde avant de continuer.', 'lejournaldesactus'); ?></p>
            </div>
            
            <div class="card">
                <h2><?php _e('Que va faire ce générateur ?', 'lejournaldesactus'); ?></h2>
                <p><?php _e('Ce générateur va créer :', 'lejournaldesactus'); ?></p>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><?php _e('8 catégories d\'articles (Actualités, Tech, Sport, Santé, etc.)', 'lejournaldesactus'); ?></li>
                    <li><?php _e('5 utilisateurs avec des rôles différents', 'lejournaldesactus'); ?></li>
                    <li><?php _e('40 articles répartis dans les différentes catégories', 'lejournaldesactus'); ?></li>
                    <li><?php _e('Des images d\'illustration pour chaque article', 'lejournaldesactus'); ?></li>
                    <li><?php _e('Des commentaires sur certains articles', 'lejournaldesactus'); ?></li>
                </ul>
            </div>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="generate_demo_content">
                <?php wp_nonce_field('generate_demo_content_nonce', 'demo_content_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Nombre d\'articles à générer', 'lejournaldesactus'); ?></th>
                        <td>
                            <select name="post_count">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="40" selected>40</option>
                                <option value="60">60</option>
                                <option value="80">80</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Catégories à créer', 'lejournaldesactus'); ?></th>
                        <td>
                            <?php foreach ($this->categories as $slug => $name) : ?>
                                <label>
                                    <input type="checkbox" name="categories[]" value="<?php echo esc_attr($slug); ?>" checked>
                                    <?php echo esc_html($name); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Créer des utilisateurs', 'lejournaldesactus'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="create_users" value="1" checked>
                                <?php _e('Créer 5 utilisateurs avec différents rôles', 'lejournaldesactus'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Générer des commentaires', 'lejournaldesactus'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="create_comments" value="1" checked>
                                <?php _e('Ajouter des commentaires aux articles', 'lejournaldesactus'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Générer le contenu de démonstration', 'lejournaldesactus'), 'primary', 'submit', true, array('id' => 'generate-content-btn')); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Générer le contenu de démonstration
     */
    public function generate_demo_content() {
        // Vérifier le nonce
        if (!isset($_POST['demo_content_nonce']) || !wp_verify_nonce($_POST['demo_content_nonce'], 'generate_demo_content_nonce')) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.', 'lejournaldesactus'));
        }
        
        // Vérifier les permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires pour effectuer cette action.', 'lejournaldesactus'));
        }
        
        // Récupérer les options
        $post_count = isset($_POST['post_count']) ? intval($_POST['post_count']) : 40;
        $selected_categories = isset($_POST['categories']) ? $_POST['categories'] : array_keys($this->categories);
        $create_users = isset($_POST['create_users']) && $_POST['create_users'] == 1;
        $create_comments = isset($_POST['create_comments']) && $_POST['create_comments'] == 1;
        
        // Créer les catégories
        $category_ids = $this->create_categories($selected_categories);
        
        // Créer les utilisateurs
        $user_ids = array();
        if ($create_users) {
            $user_ids = $this->create_users();
        }
        
        // Si aucun utilisateur n'a été créé, utiliser l'administrateur actuel
        if (empty($user_ids)) {
            $user_ids = array(get_current_user_id());
        }
        
        // Créer les articles
        $post_ids = $this->create_posts($post_count, $category_ids, $user_ids);
        
        // Créer les commentaires
        if ($create_comments && !empty($post_ids)) {
            $this->create_comments($post_ids);
        }
        
        // Rediriger vers la page d'administration avec un message de succès
        wp_redirect(add_query_arg(
            array(
                'page' => 'lejournaldesactus-demo-content',
                'generated' => '1',
                'posts' => count($post_ids),
                'categories' => count($category_ids),
                'users' => count($user_ids)
            ),
            admin_url('themes.php')
        ));
        exit;
    }
    
    /**
     * Créer les catégories
     */
    private function create_categories($selected_categories) {
        $category_ids = array();
        
        foreach ($selected_categories as $slug) {
            if (isset($this->categories[$slug])) {
                $name = $this->categories[$slug];
                
                // Vérifier si la catégorie existe déjà
                $existing_cat = get_term_by('slug', $slug, 'category');
                if ($existing_cat) {
                    $category_ids[$slug] = $existing_cat->term_id;
                    continue;
                }
                
                // Créer la catégorie
                $result = wp_insert_term(
                    $name,
                    'category',
                    array(
                        'slug' => $slug,
                        'description' => sprintf(__('Articles sur %s', 'lejournaldesactus'), strtolower($name))
                    )
                );
                
                if (!is_wp_error($result)) {
                    $category_ids[$slug] = $result['term_id'];
                }
            }
        }
        
        return $category_ids;
    }
    
    /**
     * Créer les utilisateurs
     */
    private function create_users() {
        $user_ids = array();
        
        foreach ($this->authors as $author) {
            // Vérifier si l'utilisateur existe déjà
            $username = sanitize_user(strtolower(str_replace(' ', '.', $author['name'])));
            $existing_user = get_user_by('login', $username);
            
            if ($existing_user) {
                $user_ids[] = $existing_user->ID;
                continue;
            }
            
            // Créer l'utilisateur
            $user_id = wp_insert_user(array(
                'user_login' => $username,
                'user_pass' => wp_generate_password(),
                'user_email' => $username . '@example.com',
                'display_name' => $author['name'],
                'first_name' => explode(' ', $author['name'])[0],
                'last_name' => explode(' ', $author['name'])[1],
                'description' => $author['bio'],
                'role' => $author['role']
            ));
            
            if (!is_wp_error($user_id)) {
                $user_ids[] = $user_id;
                
                // Ajouter l'avatar si possible
                $this->set_user_avatar($user_id, $author['avatar']);
            }
        }
        
        return $user_ids;
    }
    
    /**
     * Définir l'avatar de l'utilisateur
     */
    private function set_user_avatar($user_id, $avatar_filename) {
        // Cette fonction est un placeholder, car WordPress ne permet pas facilement de définir un avatar
        // En pratique, on pourrait utiliser un plugin comme Simple Local Avatars
        return true;
    }
    
    /**
     * Créer les articles
     */
    private function create_posts($count, $category_ids, $user_ids) {
        $post_ids = array();
        
        // Charger les données des articles
        $articles_data = $this->get_articles_data();
        
        // Répartir les articles entre les catégories
        $categories = array_keys($category_ids);
        $cat_count = count($categories);
        
        for ($i = 0; $i < $count; $i++) {
            // Sélectionner une catégorie
            $cat_index = $i % $cat_count;
            $category = $categories[$cat_index];
            $category_id = $category_ids[$category];
            
            // Sélectionner un auteur
            $author_id = $user_ids[array_rand($user_ids)];
            
            // Sélectionner un article aléatoire dans les données
            $article_index = $i % count($articles_data[$category]);
            $article = $articles_data[$category][$article_index];
            
            // Créer l'article
            $post_id = wp_insert_post(array(
                'post_title' => $article['title'],
                'post_content' => $article['content'],
                'post_status' => 'publish',
                'post_author' => $author_id,
                'post_category' => array($category_id),
                'post_date' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                'post_excerpt' => $article['excerpt']
            ));
            
            if (!is_wp_error($post_id)) {
                $post_ids[] = $post_id;
                
                // Ajouter une image mise en avant
                $this->set_featured_image($post_id, $category, $article_index);
            }
        }
        
        return $post_ids;
    }
    
    /**
     * Définir l'image mise en avant
     */
    private function set_featured_image($post_id, $category, $index) {
        // Chemin vers les images de démonstration
        $image_dir = get_template_directory() . '/assets/images/demo/' . $category;
        
        // Si le répertoire n'existe pas, utiliser un répertoire générique
        if (!file_exists($image_dir)) {
            $image_dir = get_template_directory() . '/assets/images/demo/generic';
        }
        
        // Si le répertoire n'existe toujours pas, abandonner
        if (!file_exists($image_dir)) {
            return false;
        }
        
        // Récupérer la liste des images
        $images = glob($image_dir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        if (empty($images)) {
            return false;
        }
        
        // Sélectionner une image
        $image_index = $index % count($images);
        $image_path = $images[$image_index];
        
        // Importer l'image dans la bibliothèque de médias
        $attachment_id = $this->import_image_to_media_library($image_path, $post_id);
        
        if ($attachment_id) {
            // Définir l'image comme image mise en avant
            set_post_thumbnail($post_id, $attachment_id);
            return true;
        }
        
        return false;
    }
    
    /**
     * Importer une image dans la bibliothèque de médias
     */
    private function import_image_to_media_library($image_path, $post_id) {
        // Vérifier si le fichier existe
        if (!file_exists($image_path)) {
            return false;
        }
        
        // Récupérer le type de fichier
        $filetype = wp_check_filetype(basename($image_path), null);
        
        // Préparer les données de l'attachement
        $attachment = array(
            'guid' => wp_upload_dir()['url'] . '/' . basename($image_path),
            'post_mime_type' => $filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($image_path)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        // Insérer l'attachement
        $attach_id = wp_insert_attachment($attachment, $image_path, $post_id);
        
        // Générer les métadonnées
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        return $attach_id;
    }
    
    /**
     * Créer des commentaires
     */
    private function create_comments($post_ids) {
        $comments_data = $this->get_comments_data();
        
        foreach ($post_ids as $post_id) {
            // Déterminer le nombre de commentaires pour cet article (0 à 5)
            $comment_count = rand(0, 5);
            
            for ($i = 0; $i < $comment_count; $i++) {
                // Sélectionner un commentaire aléatoire
                $comment = $comments_data[array_rand($comments_data)];
                
                // Créer le commentaire
                wp_insert_comment(array(
                    'comment_post_ID' => $post_id,
                    'comment_author' => $comment['author'],
                    'comment_author_email' => strtolower(str_replace(' ', '.', $comment['author'])) . '@example.com',
                    'comment_content' => $comment['content'],
                    'comment_date' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 20) . ' days')),
                    'comment_approved' => 1
                ));
            }
        }
        
        return true;
    }
    
    /**
     * Récupérer les données des articles
     */
    private function get_articles_data() {
        // Cette fonction sera implémentée dans un fichier séparé
        require_once get_template_directory() . '/inc/demo-content-data.php';
        return lejournaldesactus_get_demo_articles_data();
    }
    
    /**
     * Récupérer les données des commentaires
     */
    private function get_comments_data() {
        // Cette fonction sera implémentée dans un fichier séparé
        require_once get_template_directory() . '/inc/demo-content-data.php';
        return lejournaldesactus_get_demo_comments_data();
    }
}

// Initialiser la classe
$demo_content = new Lejournaldesactus_Demo_Content();
