<?php
$style = get_theme_mod('lejournaldesactus_site_style', 'standard');
if ($style === 'magazine') {
    get_header('magazine');
} else {
    get_header();
}
?>

<main id="main-content" class="main-content<?php
    $archive_layout = get_theme_mod('lejournaldesactus_archive_layout', 'grid');
    echo $archive_layout === 'list' ? ' archive-list' : ' archive-grid';
?>">

    <!-- Hero Section -->
    <section id="hero" class="hero homepage-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php
          // Articles mis en avant
          $featured_args = array(
            'posts_per_page' => 1,
            'meta_key' => '_thumbnail_id',
            'post__in' => get_option('sticky_posts'),
          );
          $featured_query = new WP_Query($featured_args);

          $excluded_ids = array(); // Initialiser un tableau pour stocker les IDs à exclure
          
          if ($featured_query->have_posts()) :
            while ($featured_query->have_posts()) : $featured_query->the_post();
              $excluded_ids[] = get_the_ID(); // Ajouter l'ID de l'article mis en avant au tableau
          ?>
                    <div class="hero-post homepage-featured-post">
                        <div class="post-img homepage-post-image">
                            <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="img-link">
                                <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php do_action('lejournaldesactus_before_post_meta'); ?>
                        <div class="post-meta">
                            <?php lejournaldesactus_post_categories(); ?>
                        </div>
                        <?php do_action('lejournaldesactus_after_post_meta'); ?>
                        <?php do_action('lejournaldesactus_before_post_title'); ?>
                        <h1 class="post-title homepage-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php echo lejda_show_badge_new(get_the_ID()); ?><?php echo lejda_show_badge_popular(get_the_ID()); ?></h1>
                        <?php do_action('lejournaldesactus_after_post_title'); ?>
                        <?php do_action('lejournaldesactus_before_post_content'); ?>
                        <div class="post-content homepage-post-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="<?php the_permalink(); ?>"
                                class="read-more"><?php esc_html_e('Lire la suite', 'lejournaldesactus'); ?> <i
                                    class="bi bi-arrow-right"></i></a>
                            <div class="social-share">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-twitter"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php
            endwhile;
            wp_reset_postdata();
          endif;
          ?>
                </div>

                <div class="col-lg-4">
                    <?php
          // Articles récents pour la sidebar
          $recent_args = array(
            'posts_per_page' => 3,
            'post__not_in' => array_merge(get_option('sticky_posts'), $excluded_ids),
          );
          $recent_query = new WP_Query($recent_args);
          
          if ($recent_query->have_posts()) :
            while ($recent_query->have_posts()) : $recent_query->the_post();
          ?>
                    <div class="post-entry-1 homepage-sidebar-post">
                        <?php do_action('lejournaldesactus_before_post_meta'); ?>
                        <div class="post-meta">
                            <?php lejournaldesactus_post_categories(); ?>
                        </div>
                        <?php do_action('lejournaldesactus_after_post_meta'); ?>
                        <?php do_action('lejournaldesactus_before_post_title'); ?>
                        <h2 class="post-title homepage-sidebar-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php echo lejda_show_badge_new(get_the_ID()); ?><?php echo lejda_show_badge_popular(get_the_ID()); ?></h2>
                        <?php do_action('lejournaldesactus_after_post_title'); ?>
                        <?php do_action('lejournaldesactus_before_post_content'); ?>
                        <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="post-img">
                            <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                        </a>
                        <?php endif; ?>
                        <div class="post-content homepage-sidebar-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                    <?php
            endwhile;
            wp_reset_postdata();
          endif;
          ?>
                </div>
            </div>
        </div>
    </section><!-- /Hero Section -->

    <!-- Posts Section -->
    <?php $cols = intval(get_theme_mod('lejournaldesactus_home_columns', 3)); ?>
    <section id="posts" class="posts homepage-posts-section home-cols-<?php echo $cols; ?>">
        <div class="container" data-aos="fade-up">
            <div class="section-header d-flex justify-content-between align-items-center mb-5">
                <h2><?php echo get_theme_mod('lejournaldesactus_latest_posts_title', esc_html__('Articles récents', 'lejournaldesactus')); ?>
                </h2>
                <div class="more-posts">
                    <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>"><?php esc_html_e('Tous les articles', 'lejournaldesactus'); ?>
                        <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="row g-5">
                        <?php
            // Articles principaux
            $main_args = array(
              'posts_per_page' => 6,
              'offset' => 4,
              'post__not_in' => get_option('sticky_posts'),
            );
            $main_query = new WP_Query($main_args);
            
            if ($main_query->have_posts()) :
              $count = 0;
              while ($main_query->have_posts()) : $main_query->the_post();
                $count++;
                // Affichage d'un article sous forme de carte
                echo '<div class="home-col">';
                echo '<div class="post-box post-img">';
                echo '<div class="post-meta">';
                do_action('lejournaldesactus_before_post_meta');
                lejournaldesactus_post_categories();
                echo '</div>';
                do_action('lejournaldesactus_after_post_meta');
                do_action('lejournaldesactus_before_post_title');
                echo '<h3 class="post-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a>' . lejda_show_badge_new(get_the_ID()) . lejda_show_badge_popular(get_the_ID()) . '</h3>';
                do_action('lejournaldesactus_after_post_title');
                do_action('lejournaldesactus_before_post_content');
                if (has_post_thumbnail()) {
                  echo '<div class="post-img">';
                  echo '<a href="' . get_permalink() . '" class="img-link">';
                  the_post_thumbnail('large', array('class' => 'img-fluid'));
                  echo '</a>';
                  echo '</div>';
                }
                echo '<div class="post-content">';
                the_excerpt();
                echo '</div>';
                echo '<div class="d-flex align-items-center justify-content-between">';
                echo '<a href="' . get_permalink() . '" class="read-more">' . esc_html__('Lire la suite', 'lejournaldesactus') . ' <i class="bi bi-arrow-right"></i></a>';
                echo '<div class="social-share">';
                echo '<a href="#"><i class="bi bi-facebook"></i></a>';
                echo '<a href="#"><i class="bi bi-twitter"></i></a>';
                echo '<a href="#"><i class="bi bi-instagram"></i></a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
              endwhile;
              wp_reset_postdata();
            endif;
            ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <?php get_sidebar(); ?>
                </div>
            </div>
        </div>
    </section><!-- /Posts Section -->

    <!-- Call To Action Section -->
    <section id="cta" class="cta">
        <div class="container" data-aos="zoom-in">
            <div class="row g-5">
                <div class="col-lg-12 text-center text-lg-start">
                    <h3 class="cta-title">
                        <?php echo get_theme_mod('lejournaldesactus_cta_title', esc_html__('Abonnez-vous à notre newsletter', 'lejournaldesactus')); ?>
                    </h3>
                    <p class="cta-text">
                        <?php echo get_theme_mod('lejournaldesactus_cta_text', esc_html__('Restez informé de nos derniers articles et actualités. Rejoignez notre communauté dès aujourd\'hui !', 'lejournaldesactus')); ?>
                    </p>
                    <div class="newsletter">
                        <form action="<?php echo get_template_directory_uri(); ?>/process-newsletter.php" method="post"
                            id="newsletter-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control"
                                            placeholder="<?php _e('Votre nom', 'lejournaldesactus'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control"
                                            placeholder="<?php _e('Votre email', 'lejournaldesactus'); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="newsletter-preferences mt-4">
                                <h4><?php _e('Préférences de notification', 'lejournaldesactus'); ?> <small
                                        class="text-muted">(<?php _e('optionnel', 'lejournaldesactus'); ?>)</small></h4>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="immediate_notifications"
                                        name="immediate_notifications" value="1">
                                    <label class="form-check-label" for="immediate_notifications">
                                        <?php _e('Je souhaite recevoir des notifications immédiates pour les nouveaux articles', 'lejournaldesactus'); ?>
                                    </label>
                                </div>

                                <h4><?php _e('Catégories préférées', 'lejournaldesactus'); ?> <small
                                        class="text-muted">(<?php _e('optionnel', 'lejournaldesactus'); ?>)</small></h4>
                                <div class="newsletter-categories row">
                                    <?php
                  $categories = get_categories(array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => false,
                  ));
                  
                  if (!empty($categories)) {
                    foreach ($categories as $category) {
                      ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                id="cat_<?php echo $category->term_id; ?>" name="preferred_categories[]"
                                                value="<?php echo $category->term_id; ?>">
                                            <label class="form-check-label" for="cat_<?php echo $category->term_id; ?>">
                                                <?php echo esc_html($category->name); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php
                    }
                  }
                  ?>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="gdpr_consent"
                                        name="gdpr_consent" required>
                                    <label class="form-check-label" for="gdpr_consent">
                                        <?php echo stripslashes(wp_kses_post(get_option('lejournaldesactus_privacy_text', __('J\'accepte de recevoir la newsletter et je comprends que je peux me désinscrire à tout moment.', 'lejournaldesactus')))); ?>
                                    </label>
                                </div>
                            </div>

                            <?php wp_nonce_field('lejournaldesactus_newsletter_subscribe', 'newsletter_nonce'); ?>
                            <input type="hidden" name="action" value="lejournaldesactus_newsletter_subscribe">

                            <button type="submit"
                                class="btn btn-primary mt-3"><?php _e('S\'abonner', 'lejournaldesactus'); ?></button>

                            <div class="newsletter-message mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Call To Action Section -->

</main>

<?php
// Helpers badges "Nouveau" et "Populaire"
function lejda_show_badge_new($post_id) {
    if (!get_theme_mod('lejournaldesactus_blog_badge_new', true)) return '';
    $days = intval(get_theme_mod('lejournaldesactus_blog_badge_new_days', 3));
    $date = get_the_date('U', $post_id);
    if ((time() - $date) < ($days * 86400)) {
        return '<span class="badge badge-new ms-1">' . esc_html__('Nouveau', 'lejournaldesactus') . '</span>';
    }
    return '';
}
function lejda_show_badge_popular($post_id) {
    if (!get_theme_mod('lejournaldesactus_blog_badge_popular', true)) return '';
    $threshold = intval(get_theme_mod('lejournaldesactus_blog_badge_popular_threshold', 500));
    $views = intval(get_post_meta($post_id, 'lejda_post_views', true));
    if ($views >= $threshold) {
        return '<span class="badge badge-popular ms-1">' . esc_html__('Populaire', 'lejournaldesactus') . '</span>';
    }
    return '';
}
?>

<?php
if ($style === 'magazine') {
    get_footer('magazine');
} else {
    get_footer();
}
?>