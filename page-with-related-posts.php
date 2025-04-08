<?php
/**
 * Template Name: Page avec articles liés
 * 
 * Template pour afficher une page avec les articles liés à son thème
 */

get_header();
?>

<main id="main">

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

          <?php endwhile; ?>
          
          <!-- Section Articles Liés -->
          <?php
          // Utiliser la fonction de la classe pour afficher les articles liés
          // Cette fonction récupère les articles en fonction de la catégorie ou du tag sélectionné
          global $lejournaldesactus_page_related_posts;
          if ($lejournaldesactus_page_related_posts) {
              $lejournaldesactus_page_related_posts->display_page_related_posts(get_the_ID());
          } else {
              // Fallback au cas où la classe n'est pas disponible
          ?>
          <div class="related-posts-section mt-5">
            <h2 class="section-title">Articles liés à cette page</h2>
            
            <?php
            // Récupérer la catégorie associée à la page si elle existe
            $page_id = get_the_ID();
            $category_id = get_post_meta($page_id, '_lejournaldesactus_page_related_posts_category', true);
            
            // Construire la requête
            $query_args = array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 6,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            
            // Si une catégorie est définie, l'utiliser
            if (!empty($category_id)) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'term_id',
                        'terms'    => intval($category_id),
                    ),
                );
            }
            
            $related_query = new WP_Query($query_args);
            
            if ($related_query->have_posts()) :
            ?>
            
            <div class="row">
              <?php
              while ($related_query->have_posts()) : $related_query->the_post();
              ?>
              <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                  <?php if (has_post_thumbnail()) : ?>
                  <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('medium', array('class' => 'card-img-top')); ?>
                  </a>
                  <?php endif; ?>
                  
                  <div class="card-body">
                    <h5 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                    
                    <div class="card-text">
                      <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                    </div>
                  </div>
                  
                  <div class="card-footer">
                    <small class="text-muted"><?php echo get_the_date(); ?></small>
                  </div>
                </div>
              </div>
              <?php
              endwhile;
              wp_reset_postdata();
              ?>
            </div>
            
            <?php else : ?>
            <div class="alert alert-info">
              Aucun article lié trouvé. <a href="<?php echo esc_url(home_url('/')); ?>">Voir tous les articles</a>.
            </div>
            <?php endif; ?>
          </div>
          <?php } ?>
          <!-- Fin Section Articles Liés -->

          <?php
          // If comments are open or we have at least one comment, load up the comment template.
          if (comments_open() || get_comments_number()) :
            comments_template();
          endif;
          ?>

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
