<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php if (is_dark_mode_active()) echo 'data-theme="dark"'; ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta name="google-site-verification" content="jkVDgWAXA6xncHLmfwMBM_qmyCjAdl-PEXTYZ1xTQP4" />
  <!-- Font Awesome pour les icônes sociales -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

  <!-- Overlay pour le menu mobile -->
  <div class="mobile-nav-overlay"></div>

  <header id="header" class="header header-section position-relative">
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

          <!-- Boutons d'accessibilité -->
          <!-- Suppression du bouton contraste élevé -->
          <button id="toggle-dyslexia" aria-pressed="false" class="btn btn-outline-secondary ms-2" type="button" style="min-width:44px;min-height:44px;" title="Police dyslexie" aria-label="Activer la police dyslexie-friendly">
            <i class="fa-solid fa-font"></i>
          </button>

          <form class="search-form ms-4" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="text" placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'lejournaldesactus'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" class="form-control">
            <button type="submit" class="btn btn-accent"><i class="bi bi-search"></i></button>
          </form>
          
          <!-- Bouton Menu Mobile -->
          <i class="bi bi-list mobile-nav-toggle d-xl-none" id="mobile-menu-btn" style="color: var(--light-text); transition: color 0.3s ease;"></i>
        </div>
      </div>

    </div>

    <div class="nav-wrap">
      <div class="container d-flex justify-content-between align-items-center">
        <nav id="navbar" class="navmenu">
          <?php
          wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'header-navigation',
            'fallback_cb' => false
          ));
          ?>
        </nav>
      </div>
    </div>
  </header>

  <div class="mobile-menu-header">
    <div class="mobile-menu-title">
      <h3>Menu</h3>
      <i class="bi bi-x mobile-menu-close"></i>
    </div>
    <div class="header-menu-items">
      <?php
      wp_nav_menu(array(
        'theme_location' => 'primary',
        'container' => false,
        'menu_class' => 'mobile-menu-list',
        'fallback_cb' => false,
        'items_wrap' => '<ul class="%2$s" style="background-color: transparent !important;">%3$s</ul>',
        'walker' => new Custom_Menu_Walker()
      ));
      ?>
    </div>
    <div class="mobile-social-links">
      <?php lejournaldesactus_display_social_links('mobile'); ?>
    </div>
  </div>
