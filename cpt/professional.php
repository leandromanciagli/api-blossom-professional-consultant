<?php

function create_professionals_cpt() {
    $labels = array(
        'name' => 'Profesionales',
        'singular_name' => 'Profesional',
        'menu_name' => 'Profesionales',
        'all_items' => 'Todos los Profesionales',
        'add_new' => 'Añadir Nuevo',
        'add_new_item' => 'Añadir Nuevo Profesional',
        'edit_item' => 'Editar Profesional',
        'new_item' => 'Nuevo Profesional',
        'view_item' => 'Ver Profesional',
        'search_items' => 'Buscar Profesionales',
        'not_found' => 'No se encontraron Profesionales',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'profesionales'),
        'supports' => array('title', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-businesswoman',
        'show_in_rest' => true,
    );

    register_post_type('professional', $args);
}
add_action('init', 'create_professionals_cpt');

?>