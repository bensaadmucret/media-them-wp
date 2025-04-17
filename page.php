<?php get_header(); ?>

<main id="main-content">

  <!-- Page Content Section -->
  <section class="page-content">
    <div class="container" data-aos="fade-up">

      <div class="row g-5">
        <?php
        // Récupérer le template choisi dans les options de personnalisation
        $template = get_theme_mod('lejournaldesactus_page_template', 'with-sidebar');
        
        // Définir la classe de la colonne en fonction du template
        $column_class = ($template === 'with-sidebar') ? 'col-lg-8' : 'col-lg-12';
        ?>
        
        <div class="<?php echo esc_attr($column_class); ?>">

          <?php
          while (have_posts()) :
            the_post();
          ?>
          <article class="page-entry">
            
            <?php if (has_post_thumbnail()) : ?>
            <div class="post-img">
              <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
            </div>
            <?php endif; ?>

            <?php lejournaldesactus_breadcrumb(); ?>

            <h1 class="title"><?php the_title(); ?></h1>

            <div class="content">
              <?php the_content(); ?>
            </div>

            <?php
            wp_link_pages(
              array(
                'before' => '<div class="page-links">',
                'after'  => '</div>',
              )
            );
            ?>
          </article>

          <?php
          // If comments are open or we have at least one comment, load up the comment template.
          if (comments_open() || get_comments_number()) :
            comments_template();
          endif;
          ?>

          <?php endwhile; ?>

        </div>
        
        <?php if ($template === 'with-sidebar') : ?>
        <div class="col-lg-4">
          <?php get_sidebar(); ?>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </section><!-- End Page Content Section -->

</main>

<?php get_footer(); ?>
