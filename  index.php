<?php get_header(); ?>

<main id="main">

  <!-- Hero Section -->
  <section id="hero" class="hero">
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
          
          if ($featured_query->have_posts()) :
            while ($featured_query->have_posts()) : $featured_query->the_post();
          ?>
          <div class="hero-post">
            <div class="post-meta">
              <?php blogy_post_categories(); ?>
            </div>
            <h1 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
            <?php blogy_post_meta(); ?>
            <div class="post-img">
              <?php if (has_post_thumbnail()) : ?>
                <a href="<?php the_permalink(); ?>" class="img-link">
                  <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
                </a>
              <?php endif; ?>
            </div>
            <div class="post-content">
              <?php the_excerpt(); ?>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <a href="<?php the_permalink(); ?>" class="read-more">Read More <i class="bi bi-arrow-right"></i></a>
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
            'post__not_in' => get_option('sticky_posts'),
          );
          $recent_query = new WP_Query($recent_args);
          
          if ($recent_query->have_posts()) :
            while ($recent_query->have_posts()) : $recent_query->the_post();
          ?>
          <div class="post-entry-1">
            <?php if (has_post_thumbnail()) : ?>
              <a href="<?php the_permalink(); ?>" class="post-img">
                <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
              </a>
            <?php endif; ?>
            <div class="post-meta">
              <?php blogy_post_categories(); ?>
            </div>
            <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php blogy_post_meta(); ?>
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
  <section id="posts" class="posts">
    <div class="container" data-aos="fade-up">
      <div class="section-header d-flex justify-content-between align-items-center mb-5">
        <h2><?php echo get_theme_mod('blogy_latest_posts_title', 'Latest Posts'); ?></h2>
        <div class="more-posts">
          <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>">All Articles <i class="bi bi-arrow-right"></i></a>
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
                if ($count <= 2) {
                  // Grands articles (2 premiers)
                  echo '<div class="col-lg-6">';
                  echo '<div class="post-box">';
                  if (has_post_thumbnail()) {
                    echo '<div class="post-img">';
                    echo '<a href="' . get_permalink() . '" class="img-link">';
                    the_post_thumbnail('large', array('class' => 'img-fluid'));
                    echo '</a>';
                    echo '</div>';
                  }
                  echo '<div class="post-meta">';
                  blogy_post_categories();
                  echo '</div>';
                  echo '<h3 class="post-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
                  blogy_post_meta();
                  echo '<div class="post-content">';
                  the_excerpt();
                  echo '</div>';
                  echo '<div class="d-flex align-items-center justify-content-between">';
                  echo '<a href="' . get_permalink() . '" class="read-more">Read More <i class="bi bi-arrow-right"></i></a>';
                  echo '<div class="social-share">';
                  echo '<a href="#"><i class="bi bi-facebook"></i></a>';
                  echo '<a href="#"><i class="bi bi-twitter"></i></a>';
                  echo '<a href="#"><i class="bi bi-instagram"></i></a>';
                  echo '</div>';
                  echo '</div>';
                  echo '</div>';
                  echo '</div>';
                } else {
                  // Petits articles (4 suivants)
                  if ($count == 3) {
                    echo '<div class="col-lg-12">';
                    echo '<div class="row g-5">';
                  }
                  
                  echo '<div class="col-lg-6">';
                  echo '<div class="post-entry-1">';
                  if (has_post_thumbnail()) {
                    echo '<a href="' . get_permalink() . '" class="post-img">';
                    the_post_thumbnail('medium', array('class' => 'img-fluid'));
                    echo '</a>';
                  }
                  echo '<div class="post-meta">';
                  blogy_post_categories();
                  echo '</div>';
                  echo '<h2 class="post-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
                  blogy_post_meta();
                  echo '</div>';
                  echo '</div>';
                  
                  if ($count == 6) {
                    echo '</div>';
                    echo '</div>';
                  }
                }
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
          <h3 class="cta-title"><?php echo get_theme_mod('blogy_cta_title', 'Subscribe to Our Newsletter'); ?></h3>
          <p class="cta-text"><?php echo get_theme_mod('blogy_cta_text', 'Stay updated with our latest articles and news. Join our community today!'); ?></p>
          <form action="#" class="cta-form" method="post">
            <div class="row">
              <div class="col-md-5 form-group">
                <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
              </div>
              <div class="col-md-5 form-group mt-3 mt-md-0">
                <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
              </div>
              <div class="col-md-2 form-group mt-3 mt-md-0">
                <button type="submit" class="btn btn-primary">Subscribe</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section><!-- /Call To Action Section -->

</main>

<?php get_footer(); ?>