<?php

if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
        'key' => 'group_course_fields',
        'title' => 'Datos del Curso',
        'fields' => array(
            array(
                'key' => 'field_descripcion',
                'label' => 'Descripción',
                'name' => 'descrip',
                'type' => 'textarea',
                'required' => 1,
            ),
            array(
                'key' => 'field_topic',
                'label' => 'Temática',
                'placeholder' => 'Seleccioná una temática',
                'name' => 'topic',
                'type' => 'taxonomy',
                'taxonomy' => 'topic',
                'field_type' => 'select',
                'allow_null' => 1,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
                'required' => 1,
            ),
            array(
                'key' => 'field_profesional',
                'label' => 'Profesional',
                'placeholder' => 'Seleccioná un profesional',
                'name' => 'professional',
                'type' => 'post_object',
                'post_type' => array('professional'),
                'return_format' => 'id',
                'allow_null' => 1,
                'ui' => 1,
                'required' => 1,
            ),
            array(
                'key' => 'field_precio',
                'label' => 'Precio',
                'name' => 'price',
                'type' => 'number',
                'required' => 1,
            ),
            array(
                'key' => 'field_duracion',
                'label' => 'Duración',
                'name' => 'duration',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_imagen_portada',
                'label' => 'Imagen de Portada',
                'name' => 'image_cover',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'required' => 1,
            ),
            array(
                'key' => 'field_video_preview',
                'label' => 'Video de Vista Previa',
                'name' => 'video_preview',
                'type' => 'file',
                'return_format' => 'url',
                'library' => 'all',
                'mime_types' => 'mp4,webm,ogg',
                'required' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'course',
                ),
            ),
        ),
    ));
}

// Ocultar metabox de topicos
function hide_topic_metabox() {
    remove_meta_box('tagsdiv-topic', 'course', 'side');
}
add_action('admin_menu', 'hide_topic_metabox');

// Ocultar registro de campos personalizados
// function hide_course_custom_fields() {
//     remove_meta_box('postcustom', 'course', 'normal');
// }
// add_action('admin_menu', 'hide_course_custom_fields');