<?php
// AJAX handler pour retourner la liste des menus WordPress pour le builder visuel
add_action('wp_ajax_lejournaldesactus_get_menus', function() {
    $menus = array();
    foreach (wp_get_nav_menus() as $menu) {
        $menus[] = array('slug' => $menu->slug, 'name' => $menu->name);
    }
    echo json_encode($menus);
    wp_die();
});
