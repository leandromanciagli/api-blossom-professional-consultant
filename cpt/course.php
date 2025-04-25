<?php

function create_courses_cpt() {
    $labels = array(
        'name' => 'Cursos',
        'singular_name' => 'Curso',
        'menu_name' => 'Cursos',
        'all_items' => 'Todos los Cursos',
        'add_new' => 'Añadir Nuevo',
        'add_new_item' => 'Añadir Nuevo Curso',
        'edit_item' => 'Editar Curso',
        'new_item' => 'Nuevo Curso',
        'view_item' => 'Ver Curso',
        'search_items' => 'Buscar Cursos',
        'not_found' => 'No se encontraron Cursos',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'cursos'),
        'supports' => array('title', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-welcome-learn-more',
        'show_in_rest' => true,
    );

    register_post_type('course', $args);
}
add_action('init', 'create_courses_cpt');


?>