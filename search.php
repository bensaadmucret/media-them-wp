<?php get_header(); ?>

<main id="main">

  <!-- Search Results Section -->
  <section class="search-results-section">
    <div class="container" data-aos="fade-up">
      <div class="section-header d-flex justify-content-between align-items-center mb-5">
        <h2>
          <?php
          printf(
            esc_html__('Résultats de recherche pour : %s', 'lejournaldesactus'),
            '<span>' . get_search_query() . '</span>'
          );
          ?>
        </h2>
      </div>

      <div class="row g-5">
        <div class="col-lg-8">
          <?php if (have_posts()) : ?>
            <div class="row g-5">
              <?php
              while (have_posts()) : the_post();
              ?>
              <div class="col-lg-6">
                <div class="post-entry-1">
                  <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="post-img">
                      <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                    </a>
                  <?php endif; ?>
                  <div class="post-meta">
                    <span class="date"><i class="bi bi-clock"></i> <?php echo get_the_date(); ?></span>
                    <span class="author"><i class="bi bi-person"></i> <?php the_author(); ?></span>
                  </div>
                  <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                  <div class="post-excerpt">
                    <?php the_excerpt(); ?>
                  </div>
                  <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Lire la suite', 'lejournaldesactus'); ?> <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
              <?php endwhile; ?>
            </div>

            <div class="pagination-container mt-5">
              <?php
              echo paginate_links(array(
                'prev_text' => '<i class="bi bi-arrow-left"></i>',
                'next_text' => '<i class="bi bi-arrow-right"></i>',
                'type'      => 'list',
                'before_page_number' => '<span class="page-number">',
                'after_page_number'  => '</span>',
              ));
              ?>
            </div>
          <?php else : ?>
            <div class="no-results">
              <h3><?php esc_html_e('Aucun résultat trouvé', 'lejournaldesactus'); ?></h3>
              <p><?php esc_html_e('Désolé, mais rien ne correspond à vos termes de recherche. Veuillez réessayer avec des mots-clés différents.', 'lejournaldesactus'); ?></p>
              <div class="search-form-container mt-4">
                <?php get_search_form(); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="col-lg-4">
          <?php get_sidebar(); ?>
        </div>
      </div>
    </div>
  </section><!-- End Search Results Section -->

</main>

<?php get_footer(); ?>
