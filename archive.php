<?php get_header(); ?>

<main id="main" class="main-content<?php
    $archive_layout = get_theme_mod('lejournaldesactus_archive_layout', 'grid');
    echo $archive_layout === 'list' ? ' archive-list' : ' archive-grid';
?>">

  <!-- Archive Section -->
  <section class="archive-section">
    <div class="container" data-aos="fade-up">
      <div class="section-header d-flex justify-content-between align-items-center mb-5">
        <?php do_action('lejournaldesactus_before_post_title'); ?>
        <h2>
          <?php
          if (is_category()) {
            echo single_cat_title('', false);
          } elseif (is_tag()) {
            echo single_tag_title('', false);
          } elseif (is_author()) {
            the_post();
            echo 'Articles par ' . get_the_author();
            rewind_posts();
          } elseif (is_day()) {
            echo 'Archives du jour: ' . get_the_date();
          } elseif (is_month()) {
            echo 'Archives du mois: ' . get_the_date('F Y');
          } elseif (is_year()) {
            echo 'Archives de l\'année: ' . get_the_date('Y');
          } else {
            echo 'Archives';
          }
          ?>
        </h2>
        <?php do_action('lejournaldesactus_after_post_title'); ?>
      </div>

      <div class="row g-5">
        <div class="col-lg-8">
          <?php if (have_posts()) : ?>
            <div class="row g-5">
              <?php
              $count = 0;
              while (have_posts()) : the_post();
                $count++;
                if ($count <= 2) {
                  // Grands articles (2 premiers)
                  ?>
                  <div class="col-lg-6">
                    <div class="post-box">
                      <?php if (has_post_thumbnail()) : ?>
                        <div class="post-img">
                          <a href="<?php the_permalink(); ?>" class="img-link">
                            <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                          </a>
                        </div>
                      <?php endif; ?>
                      <div class="post-meta">
                        <?php do_action('lejournaldesactus_post_meta'); ?>
                        <span class="date"><i class="bi bi-clock"></i> <?php echo get_the_date(); ?></span>
                        <span class="author"><i class="bi bi-person"></i> <?php the_author(); ?></span>
                      </div>
                      <?php do_action('lejournaldesactus_before_post_title'); ?>
                      <h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                      <?php do_action('lejournaldesactus_after_post_title'); ?>
                      <?php do_action('lejournaldesactus_before_post_content'); ?>
                      <div class="post-content">
                        <?php the_excerpt(); ?>
                      </div>
                      <div class="d-flex align-items-center justify-content-between">
                        <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Lire la suite', 'lejournaldesactus'); ?> <i class="bi bi-arrow-right"></i></a>
                        <div class="social-share">
                          <a href="#"><i class="bi bi-facebook"></i></a>
                          <a href="#"><i class="bi bi-twitter"></i></a>
                          <a href="#"><i class="bi bi-instagram"></i></a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php
                } else {
                  // Petits articles (reste)
                  if ($count == 3) {
                    echo '<div class="col-lg-12 mt-5">';
                    echo '<div class="row g-5">';
                  }
                  ?>
                  <div class="col-lg-6">
                    <div class="post-entry-1">
                      <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="post-img">
                          <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                        </a>
                      <?php endif; ?>
                      <div class="post-meta">
                        <?php do_action('lejournaldesactus_post_meta'); ?>
                        <span class="date"><i class="bi bi-clock"></i> <?php echo get_the_date(); ?></span>
                        <span class="author"><i class="bi bi-person"></i> <?php the_author(); ?></span>
                      </div>
                      <?php do_action('lejournaldesactus_before_post_title'); ?>
                      <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php echo lejda_show_badge_new(get_the_ID()); ?><?php echo lejda_show_badge_popular(get_the_ID()); ?></h2>
                      <?php do_action('lejournaldesactus_after_post_title'); ?>
                    </div>
                  </div>
                  <?php
                  if ($count % 2 == 0 && $count > 3) {
                    echo '</div>';
                    echo '<div class="row g-5 mt-4">';
                  }
                }
              endwhile;
              
              if ($count > 2) {
                echo '</div>';
                echo '</div>';
              }
              ?>
            </div>

            <div class="pagination-container mt-5">
              <?php
              echo paginate_links(array(
                'prev_text' => '<i class="bi bi-arrow-left"></i>',
                'next_text' => '<i class="bi bi-arrow-right"></i>',
                'type'      => 'list',
                'class'     => 'pagination',
              ));
              ?>
            </div>
          <?php else : ?>
            <p><?php esc_html_e('Aucun article trouvé.', 'blogy'); ?></p>
          <?php endif; ?>
        </div>

        <div class="col-lg-4">
          <?php get_sidebar(); ?>
        </div>
      </div>
    </div>
  </section><!-- End Archive Section -->

</main>

<?php 
// Helpers badges "Nouveau" et "Populaire"
if (!function_exists('lejda_show_badge_new')) {
function lejda_show_badge_new($post_id) {
    if (!get_theme_mod('lejournaldesactus_blog_badge_new', true)) return '';
    $days = intval(get_theme_mod('lejournaldesactus_blog_badge_new_days', 3));
    $date = get_the_date('U', $post_id);
    if ((time() - $date) < ($days * 86400)) {
        return '<span class="badge badge-new ms-1">' . esc_html__('Nouveau', 'lejournaldesactus') . '</span>';
    }
    return '';
}}
if (!function_exists('lejda_show_badge_popular')) {
function lejda_show_badge_popular($post_id) {
    if (!get_theme_mod('lejournaldesactus_blog_badge_popular', true)) return '';
    $threshold = intval(get_theme_mod('lejournaldesactus_blog_badge_popular_threshold', 500));
    $views = intval(get_post_meta($post_id, 'lejda_post_views', true));
    if ($views >= $threshold) {
        return '<span class="badge badge-popular ms-1">' . esc_html__('Populaire', 'lejournaldesactus') . '</span>';
    }
    return '';
}}
?>

<?php get_footer(); ?>
