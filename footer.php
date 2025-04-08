<footer id="footer" class="footer">

  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6 footer-about">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo d-flex align-items-center">
          <span class="sitename"><?php bloginfo('name'); ?></span>
        </a>
    
        <div class="mt-4">
          <?php 
          // Afficher les liens vers les réseaux sociaux dans le footer
          lejournaldesactus_display_social_links('footer'); 
          ?>
        </div>
      </div>

      <!-- Footer Widget Zone 1 -->
      <div class="col-lg-2 col-md-3 footer-links">
        <?php if (is_active_sidebar('footer-1')) : ?>
          <?php dynamic_sidebar('footer-1'); ?>
        <?php else : ?>
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
        <?php endif; ?>
      </div>

      <!-- Footer Widget Zone 2 -->
      <div class="col-lg-2 col-md-3 footer-links">
        <?php if (is_active_sidebar('footer-2')) : ?>
          <?php dynamic_sidebar('footer-2'); ?>
        <?php else : ?>
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
        <?php endif; ?>
      </div>

      <!-- Footer Widget Zone 3 -->
      <div class="col-lg-2 col-md-3 footer-links">
        <?php if (is_active_sidebar('footer-3')) : ?>
          <?php dynamic_sidebar('footer-3'); ?>
        <?php else : ?>
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
        <?php endif; ?>
      </div>

      <!-- Footer Widget Zone 4 -->
      <div class="col-lg-2 col-md-3 footer-links">
        <?php if (is_active_sidebar('footer-4')) : ?>
          <?php dynamic_sidebar('footer-4'); ?>
        <?php else : ?>
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
        <?php endif; ?>
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