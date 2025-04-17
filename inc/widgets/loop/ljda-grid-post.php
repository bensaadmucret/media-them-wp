<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Inclusion des éléments de post (adaptés)
require_once get_template_directory() . '/inc/widgets/loop/ljda-post-elements.php';

// Widget Instance
$instance = $args['instance'];
$index = isset( $instance['_post_index'] ) ? $instance['_post_index'] : 0;
$layout = isset( $instance['layout'] ) ? $instance['layout'] : '';
$widget = str_contains( $layout, 'list' ) ? 'list' : 'grid';

// Post Class
$post_class = implode( ' ', get_post_class( 'ljda-grid-item', get_the_ID() ) );

// Grille : classes supplémentaires selon le layout ou l’index
switch ( $layout ) {
    case 'list-1':
    case 'list-2':
    case 'list-3':
    case 'list-4':
    case 'list-5':
        if ( 1 === $index ) {
            $post_class .= ' ljda-big-post';
        }
        break;
    case 'list-6':
        if ( 2 >= $index ) {
            $post_class .= ' ljda-big-post';
        }
        break;
}

echo '<article class="'. esc_attr( $post_class ) .'">';
    // Above Media
    if ( isset( $instance['_el_locations']['above'] ) ) :
    echo '<div class="ljda-grid-above-media">';
        ljda_get_post_elements_by_location( $instance, 'above' );
    echo '</div>';
    endif;
    // Media
    ljda_post_thumbnail( $instance, $widget );
    // Below Media
    if ( isset( $instance['_el_locations']['below'] ) ) :
    echo '<div class="ljda-grid-below-media">';
        ljda_get_post_elements_by_location( $instance, 'below' );
    echo '</div>';
    endif;
echo '</article>';
