<div class="sidebar">

  <?php if (is_active_sidebar('sidebar-1')) : ?>
    <?php dynamic_sidebar('sidebar-1'); ?>
  <?php else : ?>

    <!-- Default Sidebar Content -->
    <div class="sidebar-item search-form">
      <h4 class="sidebar-title"><?php esc_html_e('Rechercher', 'lejournaldesactus'); ?></h4>
      <form action="<?php echo esc_url(home_url('/')); ?>" class="mt-3">
        <input type="text" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr_x('Rechercher...', 'placeholder', 'lejournaldesactus'); ?>">
        <button type="submit" class="btn-accent"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End sidebar search form-->

    <div class="sidebar-item categories">
      <h4 class="sidebar-title"><?php esc_html_e('Catégories', 'lejournaldesactus'); ?></h4>
      <ul class="mt-3">
        <?php
        $categories = get_categories(array(
          'orderby' => 'name',
          'order'   => 'ASC'
        ));
        
        foreach ($categories as $category) {
          printf(
            '<li><a href="%1$s">%2$s <span>(%3$s)</span></a></li>',
            esc_url(get_category_link($category->term_id)),
            esc_html($category->name),
            esc_html($category->count)
          );
        }
        ?>
      </ul>
    </div><!-- End sidebar categories-->

    <div class="sidebar-item recent-posts">
      <h4 class="sidebar-title"><?php esc_html_e('Articles récents', 'lejournaldesactus'); ?></h4>

      <div class="mt-3">
        <?php
        $recent_posts = wp_get_recent_posts(array(
          'numberposts' => 5,
          'post_status' => 'publish'
        ));
        
        foreach ($recent_posts as $post) :
          $post_id = $post['ID'];
        ?>
        <div class="post-item">
          <?php if (has_post_thumbnail($post_id)) : ?>
            <a href="<?php echo get_permalink($post_id); ?>">
              <?php echo get_the_post_thumbnail($post_id, 'thumbnail', array('class' => 'img-fluid')); ?>
            </a>
          <?php endif; ?>
          <div>
            <h5><a href="<?php echo get_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a></h5>
            <time datetime="<?php echo get_the_date('c', $post_id); ?>"><?php echo get_the_date('', $post_id); ?></time>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div><!-- End sidebar recent posts-->

    <div class="sidebar-item tags">
      <h4 class="sidebar-title"><?php esc_html_e('Étiquettes', 'lejournaldesactus'); ?></h4>
      <div class="tags-list">
        <?php
        $tags = get_tags(array(
          'orderby' => 'count',
          'order'   => 'DESC',
          'number'  => 20
        ));
        
        if ($tags) {
          foreach ($tags as $tag) {
            printf(
              '<a href="%1$s">%2$s</a>',
              esc_url(get_tag_link($tag->term_id)),
              esc_html($tag->name)
            );
          }
        }
        ?>
      </div>
    </div><!-- End sidebar tags-->

  <?php endif; ?>

</div><!-- End sidebar -->
