<?php
/**
 * Template Name: Page avec Sidebar
 * Description: Template de page avec une barre latérale.
 */

get_header();
?>

<main id="main">
  <section class="page-content">
    <div class="container" data-aos="fade-up">
      <div class="row g-5">
        <div class="col-lg-8">
          <?php
          while (have_posts()) :
            the_post();
          ?>
          <article class="page-content-main">
            <?php if (has_post_thumbnail()) : ?>
              <div class="featured-image">
                <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
              </div>
            <?php endif; ?>

            <h1 class="page-title"><?php the_title(); ?></h1>

            <div class="content">
              <?php the_content(); ?>
            </div>

            <?php
            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
              comments_template();
            endif;
            ?>
          </article>
          <?php endwhile; ?>
        </div>
        
        <div class="col-lg-4">
          <?php get_sidebar(); ?>
        </div>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
