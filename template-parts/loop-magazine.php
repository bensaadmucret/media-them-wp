<?php
// Boucle magazine : structure "magazine" pour la homepage
$is_first = true;
if (have_posts()) :
  while (have_posts()) : the_post();
    if ($is_first) : ?>
      <div class="col-12 mb-4">
        <div class="card">
          <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('large', ['class' => 'card-img-top', 'alt' => get_the_title()]); ?>
          <?php else : ?>
            <img src="https://via.placeholder.com/800x300" class="card-img-top" alt="Image d'illustration">
          <?php endif; ?>
          <div class="card-body">
            <h2 class="card-title"><?php the_title(); ?></h2>
            <p class="card-text"><?php echo get_the_excerpt(); ?></p>
            <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php esc_html_e('Lire la suite', 'lejournaldesactus'); ?></a>
          </div>
        </div>
      </div>
    <?php $is_first = false; else : ?>
      <div class="col-md-6 mb-4">
        <div class="card h-100">
          <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium', ['class' => 'card-img-top', 'alt' => get_the_title()]); ?>
          <?php else : ?>
            <img src="https://via.placeholder.com/400x200" class="card-img-top" alt="Image d'illustration">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?php the_title(); ?></h5>
            <p class="card-text"><?php echo get_the_excerpt(); ?></p>
            <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm"><?php esc_html_e('Lire', 'lejournaldesactus'); ?></a>
          </div>
        </div>
      </div>
    <?php endif;
  endwhile;
endif;
?>
