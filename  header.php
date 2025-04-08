<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

  <header id="header" class="header position-relative">
    <div class="container-fluid container-xl position-relative">

      <div class="top-row d-flex align-items-center justify-content-between">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo d-flex align-items-end">
          <?php if (has_custom_logo()): ?>
            <?php the_custom_logo(); ?>
          <?php else: ?>
            <h1 class="sitename"><?php bloginfo('name'); ?></h1><span>.</span>
          <?php endif; ?>
        </a>

        <div class="d-flex align-items-center">
          <div class="social-links">
            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
          </div>

          <form class="search-form ms-4" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="text" placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'blogy'); ?>" value="<?php echo get_search_query(); ?>" name="s" class="form-control">
            <button type="submit" class="btn"><i class="bi bi-search"></i></button>
          </form>
        </div>
      </div>

    </div>

    <div class="nav-wrap">
      <div class="container d-flex justify-content-center position-relative">
        <nav id="navmenu" class="navmenu">
          <?php
            wp_nav_menu(array(
              'theme_location' => 'primary',
              'container'      => false,
              'menu_class'     => '',
              'fallback_cb'    => '__return_false',
              'items_wrap'     => '<ul>%3$s</ul>',
              'depth'          => 2,
              'walker'         => new WP_Bootstrap_Navwalker()
            ));
          ?>
        </nav>
      </div>
    </div>

  </header>