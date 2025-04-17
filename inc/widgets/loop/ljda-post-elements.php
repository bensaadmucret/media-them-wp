<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Thumbnail (adapté)
function ljda_post_thumbnail( $instance, $widget = '', $class = '' ) {
    $class  = '' !== $class ? ' '. $class : '';
    $class .= ('magazine' === $widget) ? ' ljda-full-stretch' : '';
    $size   = ljda_get_post_thumbnail_size($instance, $widget);
    $style  = 'background-image: url('. get_the_post_thumbnail_url( get_the_ID(), $size ) .')';

    if ( has_post_thumbnail() ) {
        if ( 'magazine' === $widget ) {
            echo '<a href="'. esc_url( get_permalink() ) .'"  style="'. esc_attr($style) .'" class="ljda-grid-image'. esc_attr($class) .'" title="'. esc_attr( get_the_title() ) .'"></a>';
        } else {
            $image_link = isset($instance['image_link']) && $instance['image_link'] ? true : false;
            echo ($image_link) ? '<a href="'. esc_url( get_permalink() ) .'" class="ljda-grid-image'. esc_attr($class) .'" title="'. esc_attr( get_the_title() ) .'">' : '';
                the_post_thumbnail( $size, ['loading' => 'lazy'] );
            echo ($image_link) ? '</a>' : '';
        }
    }
}

// Thumbnail size (adapté)
function ljda_get_post_thumbnail_size( $instance, $widget ) {
    $size = isset($instance['image_size']) ? $instance['image_size'] : 'full';
    $index = isset($instance['_post_index']) ? $instance['_post_index'] : 0;
    if ( 'magazine' === $widget ) {
        $size = 'ljda-860x570';
    }
    return $size;
}

// Affichage des éléments par emplacement (adapté)
function ljda_get_post_elements_by_location( $instance, $location ) {
    if ( ! isset( $instance['_el_locations'][$location] ) ) return;
    foreach ( $instance['_el_locations'][$location] as $element ) {
        switch ( $element ) {
            case 'categories':
                echo '<div class="ljda-post-categories">';
                the_category( ', ' );
                echo '</div>';
                break;
            case 'title':
                $tag = isset($instance['title_tag']) ? $instance['title_tag'] : 'h3';
                echo '<'. $tag .' class="ljda-post-title"><a href="'. esc_url( get_permalink() ) .'">'. get_the_title() .'</a></'. $tag .'>';
                break;
            case 'meta':
                echo '<div class="ljda-post-meta">';
                echo get_the_date();
                echo '</div>';
                break;
            case 'excerpt':
                echo '<div class="ljda-post-excerpt">'. wp_trim_words( get_the_excerpt(), 20 ) .'</div>';
                break;
            case 'read_more':
                echo '<a class="ljda-read-more" href="'. esc_url( get_permalink() ) .'">'. esc_html__( 'Lire la suite', 'lejournaldesactus' ) .'</a>';
                break;
        }
    }
}
