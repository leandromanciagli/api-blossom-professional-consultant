<?php 

function register_topic_taxonomy() {
    register_taxonomy('topic', 'course', array(
        'label' => 'Temáticas',
        'rewrite' => array('slug' => 'topic'),
        'hierarchical' => false, // Se comporta como etiquetas
        'show_admin_column' => true, // Muestra la columna en el admin
    ));
}
add_action('init', 'register_topic_taxonomy');

?>