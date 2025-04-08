<?php
/**
 * WP Bootstrap Navwalker
 *
 * @package WP-Bootstrap-Navwalker
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WP_Bootstrap_Navwalker
 *
 * A custom WordPress nav walker class to implement the Bootstrap 5 navigation style.
 */
class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {

    /**
     * Starts the element output.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     Current item ID.
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';

        $classes   = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Add .dropdown or .active classes where they are needed.
        if ($this->has_children) {
            $classes[] = 'dropdown';
        }
        if (in_array('current-menu-item', $classes, true) || in_array('current-menu-parent', $classes, true)) {
            $classes[] = 'active';
        }

        // Add .dropdown-menu-end for right-aligned dropdowns.
        if (in_array('menu-item-align-right', $classes, true)) {
            $classes[] = 'dropdown-menu-end';
        }

        $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts           = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        if ('_blank' === $item->target && empty($item->xfn)) {
            $atts['rel'] = 'noopener noreferrer';
        } else {
            $atts['rel'] = $item->xfn;
        }
        $atts['href']         = !empty($item->url) ? $item->url : '';
        $atts['aria-current'] = $item->current ? 'page' : '';

        if ($this->has_children && 0 === $depth) {
            $atts['class']         = 'dropdown-toggle';
            $atts['data-bs-toggle'] = 'dropdown';
            $atts['aria-expanded'] = 'false';
        }

        if (in_array('current-menu-item', $item->classes, true)) {
            $atts['class'] = isset($atts['class']) ? $atts['class'] . ' active' : 'active';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        // Add .dropdown-item class to dropdown items.
        $link_classes = array();
        if ($depth > 0) {
            $link_classes[] = 'dropdown-item';
        }

        if (in_array('current-menu-item', $item->classes, true)) {
            $link_classes[] = 'active';
        }

        $link_class_string = '';
        if (!empty($link_classes)) {
            $link_class_string = ' class="' . implode(' ', $link_classes) . '"';
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . $link_class_string . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        
        if ($this->has_children && 0 === $depth) {
            $item_output .= ' <i class="bi bi-chevron-down toggle-dropdown"></i>';
        }
        
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Ends the element output.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Page data object.
     * @param int      $depth  Depth of page.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }

    /**
     * Starts the list before the elements are added.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);

        // Default class.
        $classes = array('dropdown-menu');

        $class_names = join(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= "{$n}{$indent}<ul$class_names>{$n}";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent  = str_repeat($t, $depth);
        $output .= "$indent</ul>{$n}";
    }
}
