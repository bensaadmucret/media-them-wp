<?php
/**
 * Template pour l'affichage d'un profil d'auteur personnalisé
 *
 * @package LeJournalDesActus
 */

// Débogage - Vérifier si ce template est bien utilisé
error_log('Template single-custom_author.php chargé pour l\'ID: ' . get_the_ID());

get_header();
?>

<main class="main">

  <!-- Page Title -->
  <div class="page-title">
    <div class="breadcrumbs">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><i class="bi bi-house"></i> <?php esc_html_e('Accueil', 'lejournaldesactus'); ?></a></li>
          <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/redacteurs/')); ?>"><?php esc_html_e('Auteurs', 'lejournaldesactus'); ?></a></li>
          <li class="breadcrumb-item active current"><?php the_title(); ?></li>
        </ol>
      </nav>
    </div>

    <div class="title-wrapper">
      <h1><?php esc_html_e('Profil d\'auteur', 'lejournaldesactus'); ?></h1>
      <p><?php esc_html_e('Découvrez les articles et les informations sur cet auteur', 'lejournaldesactus'); ?></p>
    </div>
  </div><!-- End Page Title -->

  <!-- Author Profile Section -->
  <section id="author-profile" class="author-profile section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="author-profile-1">

        <div class="row">
          <!-- Author Info -->
          <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="author-card" data-aos="fade-up">
              <div class="author-image">
                <?php if (has_post_thumbnail()) : ?>
                  <?php the_post_thumbnail('full', array('class' => 'img-fluid rounded')); ?>
                <?php else : ?>
                  <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/person/person-default.webp'); ?>" alt="<?php the_title_attribute(); ?>" class="img-fluid rounded">
                <?php endif; ?>
              </div>

              <div class="author-info">
                <h2><?php the_title(); ?></h2>
                <p class="designation"><?php echo esc_html(get_post_meta(get_the_ID(), '_author_designation', true)); ?></p>

                <div class="author-bio">
                  <?php echo esc_html(get_post_meta(get_the_ID(), '_author_bio_short', true)); ?>
                </div>

                <div class="social-links">
                  <?php $twitter = get_post_meta(get_the_ID(), '_author_twitter', true); ?>
                  <?php if (!empty($twitter)) : ?>
                    <a href="<?php echo esc_url($twitter); ?>" class="twitter" target="_blank"><i class="bi bi-twitter-x"></i></a>
                  <?php endif; ?>
                  
                  <?php $facebook = get_post_meta(get_the_ID(), '_author_facebook', true); ?>
                  <?php if (!empty($facebook)) : ?>
                    <a href="<?php echo esc_url($facebook); ?>" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
                  <?php endif; ?>
                  
                  <?php $instagram = get_post_meta(get_the_ID(), '_author_instagram', true); ?>
                  <?php if (!empty($instagram)) : ?>
                    <a href="<?php echo esc_url($instagram); ?>" class="instagram" target="_blank"><i class="bi bi-instagram"></i></a>
                  <?php endif; ?>
                  
                  <?php $linkedin = get_post_meta(get_the_ID(), '_author_linkedin', true); ?>
                  <?php if (!empty($linkedin)) : ?>
                    <a href="<?php echo esc_url($linkedin); ?>" class="linkedin" target="_blank"><i class="bi bi-linkedin"></i></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Author Content -->
          <div class="col-lg-8">
            <div class="author-content" data-aos="fade-up" data-aos-delay="200">
              <div class="content-header">
                <h3><?php esc_html_e('À propos', 'lejournaldesactus'); ?></h3>
              </div>
              <div class="content-body">
                <?php the_content(); ?>

                <?php 
                $expertise = get_post_meta(get_the_ID(), '_author_expertise', true);
                if (!empty($expertise)) :
                  $expertise_array = explode(',', $expertise);
                ?>
                <div class="expertise-areas">
                  <h4><?php esc_html_e('Domaines d\'expertise', 'lejournaldesactus'); ?></h4>
                  <div class="tags">
                    <?php foreach ($expertise_array as $domain) : ?>
                      <span><?php echo esc_html(trim($domain)); ?></span>
                    <?php endforeach; ?>
                  </div>
                </div>
                <?php endif; ?>

                <?php
                // Récupérer les articles de l'auteur
                $author_posts = lejournaldesactus_get_author_posts(get_the_ID(), 4);
                
                if (!empty($author_posts)) :
                ?>
                <div class="featured-articles mt-5">
                  <h4><?php esc_html_e('Articles à la une', 'lejournaldesactus'); ?></h4>
                  <div class="row g-4">
                    <?php 
                    $delay = 300;
                    foreach ($author_posts as $post) :
                      setup_postdata($post);
                      $categories = get_the_category();
                    ?>
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                      <article class="article-card">
                        <div class="article-img">
                          <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                          <?php endif; ?>
                        </div>
                        <div class="article-details">
                          <?php if (!empty($categories)) : ?>
                            <div class="post-category"><?php echo esc_html($categories[0]->name); ?></div>
                          <?php endif; ?>
                          <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                          <div class="post-meta">
                            <span><i class="bi bi-clock"></i> <?php echo get_the_date(); ?></span>
                            <span><i class="bi bi-chat-dots"></i> <?php comments_number('0 ' . __('Commentaire', 'lejournaldesactus'), '1 ' . __('Commentaire', 'lejournaldesactus'), '% ' . __('Commentaires', 'lejournaldesactus')); ?></span>
                          </div>
                        </div>
                      </article>
                    </div>
                    <?php 
                      $delay += 100;
                    endforeach;
                    wp_reset_postdata();
                    ?>
                  </div>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>

  </section><!-- /Author Profile Section -->

</main>

<?php
get_footer();
