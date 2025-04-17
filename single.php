<?php get_header(); ?>

<main id="main-content">
  <!-- Barre de progression de lecture -->
  <div class="reading-progress-bar"></div>
  
  <!-- Bouton de lecture sans distraction -->
  <button class="distraction-free-toggle">
    <i class="bi bi-arrows-angle-expand"></i> Lecture zen
  </button>

  <!-- Blog Details Section -->
    <div class="container dark-mode-row" data-aos="fade-up">

      <div class="row g-5 ">
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
          <article class="blog-details-content" itemscope itemtype="https://schema.org/Article">
            <meta itemprop="mainEntityOfPage" content="<?php the_permalink(); ?>">
            <meta itemprop="datePublished" content="<?php echo get_the_date('c'); ?>">
            <meta itemprop="dateModified" content="<?php echo get_the_modified_date('c'); ?>">
            <meta itemprop="author" content="<?php the_author(); ?>">
            <?php if (has_post_thumbnail()) : ?>
              <meta itemprop="image" content="<?php echo esc_url(get_the_post_thumbnail_url(null, 'full')); ?>">
            <?php endif; ?>
            <meta itemprop="headline" content="<?php the_title_attribute(); ?>">
            <meta itemprop="description" content="<?php echo esc_attr(get_the_excerpt() ? get_the_excerpt() : wp_strip_all_tags(get_the_title())); ?>">
            <div class="post-img">
              <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
              <?php endif; ?>
            </div>

            <div class="post-meta">
              <?php lejournaldesactus_post_categories(); ?>
            </div>

            <?php do_action('lejournaldesactus_before_post_title'); ?>
            <?php lejournaldesactus_breadcrumb(); ?>
            <h1 class="title"><?php the_title(); ?></h1>
            <?php do_action('lejournaldesactus_after_post_title'); ?>

            <?php do_action('lejournaldesactus_before_post_content'); ?>
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
                <?php lejournaldesactus_display_default_author(); ?>
              </div>

              <div class="post-share">
                <span><i class="bi bi-share"></i> <?php esc_html_e('Partager :', 'lejournaldesactus'); ?></span>
                <?php 
                // Afficher les liens vers les réseaux sociaux dans l'article
                lejournaldesactus_display_social_links('articles'); 
                ?>
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
        <div class="col-lg-4 sidebar">
          <?php get_sidebar(); ?>
        </div>
        <?php endif; ?>
        
      </div>
    </div>
  </section><!-- End Blog Details Section -->

</main>

<?php get_footer(); ?>