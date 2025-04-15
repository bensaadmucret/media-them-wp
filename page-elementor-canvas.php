<?php
/**
 * Template Name: Elementor Canvas
 * Description: Un modèle de page vierge, idéal pour les landing pages Elementor (aucun header, footer ou sidebar).
 * @package LeJournalDesActus
 */

if (!defined('ABSPATH')) exit;

while (have_posts()) : the_post();
    the_content();
endwhile;
