<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Ljda_Grid_Widget_Presets {
    private $instance;
    private $post_index;
    private $post_count;
    private $location;

    public function __construct( $instance, $post_index, $post_count ) {
        $this->instance = $instance;
        $this->instance['_post_index'] = $post_index;
        $this->instance['_post_count'] = $post_count;
        $this->instance['layout'] = isset ( $this->instance['layout'] ) ? $this->instance['layout'] : '2-column';
        $this->instance['elements_preset'] = isset ( $this->instance['elements_preset'] ) ? $this->instance['elements_preset'] : 's0';
        $this->instance['title_tag'] = isset ( $this->instance['title_tag'] ) ? $this->instance['title_tag'] : 'h3';
        $this->instance['excerpt_letter_count'] = isset ( $this->instance['excerpt_letter_count'] ) ? $this->instance['excerpt_letter_count'] : 230;
    }

    // Affichage du post selon le preset
    public function display() {
        $preset = $this->instance['elements_preset'];
        switch ( $preset ) {
            case 's1':
                $this->elements_preset_s1();
                break;
            case 's2':
                $this->elements_preset_s2();
                break;
            default:
                $this->elements_preset_default();
                break;
        }
        // Inclusion du template de post
        get_template_part( 'inc/widgets/loop/ljda-grid-post', '', [ 'instance' => $this->instance ] );
    }

    public function elements_preset_default() {
        $this->instance['_el_locations'] = [
            'above' => [ 'categories' ],
            'below' => [ 'title', 'meta', 'excerpt', 'read_more' ],
        ];
    }

    public function elements_preset_s1() {
        $this->instance['_el_locations'] = [
            'above' => [ 'categories', 'meta' ],
            'below' => [ 'title', 'excerpt', 'read_more' ],
        ];
    }

    public function elements_preset_s2() {
        $this->instance['_el_locations'] = [
            'above' => [ 'categories' ],
            'below' => [ 'title', 'excerpt', 'meta', 'read_more' ],
        ];
    }
}
