<?php 

if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
        'key' => 'group_professional_fields',
        'title' => 'Datos del Profesional',
        'fields' => array(
            array(
                'key' => 'field_avatar',
                'label' => 'Foto personal',
                'name' => 'avatar',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'required' => 1,
            ),
            array(
                'key' => 'field_aficiones',
                'label' => 'Aficiones',
                'name' => 'hobbies',
                'type' => 'textarea',
                'maxlength' => 300,
                'required' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'professional',
                ),
            ),
        ),
    ));
}

?>