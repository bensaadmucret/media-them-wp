<?php
/**
 * Template Name: Page avec Carrousel
 * 
 * Template pour afficher une page avec un carrousel en haut
 */

get_header();
?>

<main id="main">
  <!-- ======= Hero Section avec Carrousel ======= -->
  <section id="hero" class="hero">
    <div class="container">
      <?php
      // Récupérer l'ID du carrousel depuis les métadonnées de la page
      $carousel_id = get_post_meta(get_the_ID(), '_page_carousel_id', true);
      
      // Si un ID de carrousel est défini, l'afficher
      if (!empty($carousel_id)) {
        echo do_shortcode('[lejournaldesactus_carousel id="' . esc_attr($carousel_id) . '"]');
      }
      ?>
    </div>
  </section><!-- End Hero -->

  <!-- ======= Contenu de la page ======= -->
  <section id="page-content" class="page-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <?php
          while (have_posts()) :
            the_post();
            ?>
            <article class="page-article">
              <h1 class="page-title"><?php the_title(); ?></h1>
              <div class="page-content">
                <?php the_content(); ?>
              </div>
            </article>
            <?php
          endwhile;
          ?>
        </div>
        
        <div class="col-lg-4">
          <?php get_sidebar(); ?>
        </div>
      </div>
    </div>
  </section><!-- End Page Content -->

</main><!-- End #main -->

<?php
get_footer();
