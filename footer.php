<footer id="footer" class="footer">

  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6 footer-about">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo d-flex align-items-center">
          <span class="sitename"><?php bloginfo('name'); ?></span>
        </a>
        <div class="footer-contact pt-3">
          <p><?php echo esc_html(get_theme_mod('lejournaldesactus_address_line1', 'A108 Adam Street')); ?></p>
          <p><?php echo esc_html(get_theme_mod('lejournaldesactus_address_line2', 'New York, NY 535022')); ?></p>
          <p class="mt-3"><strong>Phone:</strong> <span><?php echo esc_html(get_theme_mod('lejournaldesactus_phone', '+1 5589 55488 55')); ?></span></p>
          <p><strong>Email:</strong> <span><?php echo esc_html(get_theme_mod('lejournaldesactus_email', 'info@example.com')); ?></span></p>
        </div>
        <div class="mt-4">
          <?php 
          // Afficher les liens vers les réseaux sociaux dans le footer
          lejournaldesactus_display_social_links('footer'); 
          ?>
        </div>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Useful Links</h4>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-useful',
            'container'      => false,
            'menu_class'     => '',
            'fallback_cb'    => '__return_false',
            'items_wrap'     => '<ul>%3$s</ul>',
            'depth'          => 1,
          ));
        ?>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Our Services</h4>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-services',
            'container'      => false,
            'menu_class'     => '',
            'fallback_cb'    => '__return_false',
            'items_wrap'     => '<ul>%3$s</ul>',
            'depth'          => 1,
          ));
        ?>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4><?php echo esc_html(get_theme_mod('lejournaldesactus_footer_title1', 'Hic solutasetp')); ?></h4>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-links1',
            'container'      => false,
            'menu_class'     => '',
            'fallback_cb'    => '__return_false',
            'items_wrap'     => '<ul>%3$s</ul>',
            'depth'          => 1,
          ));
        ?>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4><?php echo esc_html(get_theme_mod('lejournaldesactus_footer_title2', 'Nobis illum')); ?></h4>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-links2',
            'container'      => false,
            'menu_class'     => '',
            'fallback_cb'    => '__return_false',
            'items_wrap'     => '<ul>%3$s</ul>',
            'depth'          => 1,
          ));
        ?>
      </div>

    </div>
  </div>

  <div class="container copyright text-center mt-4">
    <p> <span>Copyright</span> <strong class="px-1 sitename"><?php bloginfo('name'); ?></strong> <span>All Rights Reserved</span></p>
    <div class="credits">
      <?php echo wp_kses_post(get_theme_mod('lejournaldesactus_footer_credits', 'Designed by <a href="https://lejournaldesactus.fr" rel="noopener">Le Journal des Actus</a>')); ?>
    </div>
  </div>

</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<?php wp_footer(); ?>
</body>
</html>