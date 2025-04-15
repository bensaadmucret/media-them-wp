<?php
/**
 * Template Name: Elementor Canvas + Header
 * Description: Modèle de page vierge avec header du thème, idéal pour landing page Elementor avec navigation.
 * @package LeJournalDesActus
 */

if (!defined('ABSPATH')) exit;

get_header();
?>
<nav class="navbar navbar-expand-lg navbar-light elementor-landing-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <?php bloginfo('name'); ?>
        </a>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'elementor-landing',
            'menu_class'     => 'navbar-nav ms-auto',
            'container'      => false,
            'fallback_cb'    => false,
        ));
        ?>
    </div>
</nav>
<?php

while (have_posts()) : the_post();
    the_content();
endwhile;
