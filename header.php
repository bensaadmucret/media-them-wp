<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="google-site-verification" content="jkVDgWAXA6xncHLmfwMBM_qmyCjAdl-PEXTYZ1xTQP4" />  <!-- Font Awesome pour les icônes sociales -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

  <!-- Overlay pour le menu mobile -->
  <div class="mobile-nav-overlay"></div>

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
          <?php 
          // Afficher les liens vers les réseaux sociaux dans le header
          lejournaldesactus_display_social_links('header'); 
          ?>
          
          <!-- Bouton switch Bootstrap pour le mode sombre -->
          <div class="form-check form-switch ms-3">
            <input class="form-check-input" type="checkbox" id="darkModeSwitch" role="switch">
            <label class="form-check-label" for="darkModeSwitch">Dark Mode</label>
          </div>

          <form class="search-form ms-4" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="text" placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'lejournaldesactus'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" class="form-control">
            <button type="submit" class="btn btn-accent"><i class="bi bi-search"></i></button>
          </form>
          
          <!-- Bouton Menu Mobile -->
          <i class="bi bi-list mobile-nav-toggle d-block d-lg-none" id="mobile-menu-btn"></i>
        </div>
      </div>

    </div>

    <div class="nav-wrap d-none d-lg-block d-block-mobile-active">
      <div class="container d-flex justify-content-center position-relative">
        <nav id="navmenu" class="navmenu">
          <div class="mobile-menu-header d-block d-lg-none">
            <h3><?php _e('Menu', 'lejournaldesactus'); ?></h3>
          </div>
          
          <!-- Formulaire de recherche mobile -->
          <div class="mobile-search-form d-block d-lg-none mb-4">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
              <div class="input-group">
                <input type="text" placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'lejournaldesactus'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" class="form-control">
                <button type="submit" class="btn btn-search btn-accent"><i class="bi bi-search"></i></button>
              </div>
            </form>
          </div>
          
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
    
    <!-- Liens sociaux pour le menu mobile -->
    <div class="mobile-social-links d-block d-lg-none mt-4 text-center d-block-mobile-active">
      <a href="#" class="facebook" rel="noopener"><i class="bi bi-facebook"></i> Facebook</a>
      <a href="#" class="twitter" rel="noopener"><i class="bi bi-twitter"></i> Twitter</a>
      <a href="#" class="instagram" rel="noopener"><i class="bi bi-instagram"></i> Instagram</a>
    </div>

  </header>
