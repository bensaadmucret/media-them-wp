<?php
/**
 * Template pour l'affichage de l'archive des auteurs personnalisés
 *
 * @package LeJournalDesActus
 */

// Débogage - Vérifier si ce template est bien utilisé
error_log('Template archive-custom_author.php chargé');

get_header();
?>

<main class="main">

  <!-- Page Title -->
  <div class="page-title">
    <div class="breadcrumbs">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><i class="bi bi-house"></i> <?php esc_html_e('Accueil', 'lejournaldesactus'); ?></a></li>
          <li class="breadcrumb-item active current"><?php esc_html_e('Nos Auteurs', 'lejournaldesactus'); ?></li>
        </ol>
      </nav>
    </div>

    <div class="title-wrapper">
      <h1><?php esc_html_e('Nos Auteurs', 'lejournaldesactus'); ?></h1>
      <p><?php esc_html_e('Découvrez les journalistes et rédacteurs qui contribuent à notre site', 'lejournaldesactus'); ?></p>
    </div>
  </div><!-- End Page Title -->

  <!-- Authors Section -->
  <section id="authors" class="authors section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      
      <?php if (have_posts()) : ?>
        <div class="row g-4">
          <?php 
          $delay = 100;
          while (have_posts()) : the_post(); 
            $designation = get_post_meta(get_the_ID(), '_author_designation', true);
            $bio_short = get_post_meta(get_the_ID(), '_author_bio_short', true);
            $articles_count = get_post_meta(get_the_ID(), '_author_articles_count', true) ?: 0;
          ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
              <div class="author-card h-100">
                <div class="author-image">
                  <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>">
                      <?php the_post_thumbnail('medium', array('class' => 'img-fluid rounded')); ?>
                    </a>
                  <?php else : ?>
                    <a href="<?php the_permalink(); ?>">
                      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/person/person-default.webp'); ?>" alt="<?php the_title_attribute(); ?>" class="img-fluid rounded">
                    </a>
                  <?php endif; ?>
                </div>

                <div class="author-info">
                  <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                  <?php if (!empty($designation)) : ?>
                    <p class="designation"><?php echo esc_html($designation); ?></p>
                  <?php endif; ?>

                  <?php if (!empty($bio_short)) : ?>
                    <div class="author-bio">
                      <?php echo esc_html($bio_short); ?>
                    </div>
                  <?php endif; ?>

                  <div class="author-meta d-flex align-items-center justify-content-between mt-3">
                    <div class="articles-count">
                      <i class="bi bi-file-text"></i> <?php echo sprintf(_n('%s Article', '%s Articles', $articles_count, 'lejournaldesactus'), number_format_i18n($articles_count)); ?>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Voir le profil', 'lejournaldesactus'); ?> <i class="bi bi-arrow-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
          <?php 
            $delay += 100;
            endwhile; 
          ?>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
          <?php
          the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '<i class="bi bi-arrow-left"></i> ' . __('Précédent', 'lejournaldesactus'),
            'next_text' => __('Suivant', 'lejournaldesactus') . ' <i class="bi bi-arrow-right"></i>',
            'screen_reader_text' => __('Navigation des auteurs', 'lejournaldesactus'),
          ));
          ?>
        </div>

      <?php else : ?>
        <div class="no-authors">
          <p><?php esc_html_e('Aucun auteur trouvé.', 'lejournaldesactus'); ?></p>
        </div>
      <?php endif; ?>
      
    </div>
  </section><!-- /Authors Section -->

</main>

<?php
get_footer();
