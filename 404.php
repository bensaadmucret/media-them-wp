<?php get_header(); ?>

<main id="main">

  <!-- 404 Section -->
  <section class="error-404 not-found">
    <div class="container" data-aos="fade-up">
      <div class="row justify-content-center text-center">
        <div class="col-lg-8">
          <div class="error-404-content">
            <h1>404</h1>
            <h2><?php esc_html_e('Page non trouvée', 'blogy'); ?></h2>
            <p><?php esc_html_e('La page que vous recherchez n\'existe pas ou a été déplacée.', 'blogy'); ?></p>
            
            <div class="mt-5">
              <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php esc_html_e('Retour à l\'accueil', 'blogy'); ?></a>
            </div>
            
            <div class="search-form-container mt-5">
              <h3><?php esc_html_e('Peut-être essayer une recherche ?', 'blogy'); ?></h3>
              <?php get_search_form(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section><!-- End 404 Section -->

</main>

<?php get_footer(); ?>
