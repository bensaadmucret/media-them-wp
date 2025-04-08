<?php get_header(); ?>

<main id="main">

  <!-- Blog Details Section -->
  <section id="blog-details" class="blog-details">
    <div class="container" data-aos="fade-up">

      <div class="row g-5">
        <?php
        // Récupérer le template choisi dans les options de personnalisation
        $template = get_theme_mod('lejournaldesactus_single_post_template', 'with-sidebar');
        
        // Définir la classe de la colonne en fonction du template
        $column_class = ($template === 'with-sidebar') ? 'col-lg-8' : 'col-lg-12';
        ?>
        
        <div class="<?php echo esc_attr($column_class); ?>">

          <?php
          while (have_posts()) :
            the_post();
          ?>
          <article class="blog-details-content">
            <div class="post-img">
              <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
              <?php endif; ?>
            </div>

            <div class="post-meta">
              <?php lejournaldesactus_post_categories(); ?>
            </div>

            <h1 class="title"><?php the_title(); ?></h1>

            <div class="meta-bottom">
              <?php lejournaldesactus_post_meta(); ?>
            </div><!-- End meta bottom -->

            <div class="content">
              <?php the_content(); ?>
            </div><!-- End post content -->

            <div class="post-meta">
              <?php
              // Afficher les tags
              $tags = get_the_tags();
              if ($tags) {
                echo '<div class="post-tags">';
                foreach ($tags as $tag) {
                  echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '">#' . esc_html($tag->name) . '</a>';
                }
                echo '</div>';
              }
              ?>
            </div>

            <div class="post-footer">
              <div class="post-author-info">
                <?php lejournaldesactus_display_custom_author(); ?>
              </div>

              <div class="post-share">
                <span><i class="bi bi-share"></i> <?php esc_html_e('Partager :', 'lejournaldesactus'); ?></span>
                <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank"><i class="bi bi-twitter-x"></i></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank"><i class="bi bi-facebook"></i></a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
              </div>
            </div><!-- End post footer -->

          </article><!-- End blog post -->

          <?php 
          // Afficher les articles similaires ici, avant la navigation et les commentaires
          lejournaldesactus_display_related_posts();
          ?>

          <div class="post-navigation">
            <div class="row">
              <div class="col-md-6">
                <?php previous_post_link('<div class="nav-previous"><i class="bi bi-arrow-left"></i> %link</div>'); ?>
              </div>
              <div class="col-md-6 text-end">
                <?php next_post_link('<div class="nav-next">%link <i class="bi bi-arrow-right"></i></div>'); ?>
              </div>
            </div>
          </div>

          <?php
            // Si les commentaires sont ouverts ou s'il y a au moins un commentaire, charger le template de commentaires
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
  </section><!-- End Blog Details Section -->

</main>

<?php get_footer(); ?>