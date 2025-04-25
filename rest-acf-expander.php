<?php

add_action('rest_api_init', function () {

    // Lista de campos ACF relacionales que querés expandir, agrupados por post type
    $expanded_acf_fields = [
        'course' => ['professional'], // podés agregar más
        // 'another_post_type' => ['some_field'],
    ];

    foreach ($expanded_acf_fields as $post_type => $fields) {
        foreach ($fields as $field_name) {
            register_rest_field($post_type, "{$field_name}_data", [
                'get_callback' => function ($object) use ($field_name) {
                    $related = get_field($field_name, $object['id']);
                    if (!$related) return [];

                    // ACF puede devolver un único objeto o un array
                    $related = is_array($related) ? $related : [$related];

                    return array_map(function ($item_id) {
                        $acf_fields = get_fields($item_id);
                        return array_merge([
                            'id' => $item_id,
                            'title' => get_the_title($item_id),
                            'link' => get_permalink($item_id),
                        ], $acf_fields ?: []);
                    }, $related);
                },
                'schema' => null,
            ]);
        }
    }
});

add_action('rest_api_init', function () {
    // Obtiene todos los tipos de post públicos y visibles en la API
    $post_types = get_post_types(['public' => true, 'show_in_rest' => true], 'names');

    foreach ($post_types as $post_type) {
        // Obtiene todas las taxonomías asociadas a este tipo de post
        $taxonomies = get_object_taxonomies($post_type, 'names');

        if (empty($taxonomies)) continue;

        register_rest_field($post_type, 'taxonomies_data', [
            'get_callback' => function ($post_arr) use ($taxonomies) {
                $data = [];

                foreach ($taxonomies as $taxonomy) {
                    $terms = get_the_terms($post_arr['id'], $taxonomy);

                    if (!empty($terms) && !is_wp_error($terms)) {
                        $data[$taxonomy] = array_map(function ($term) {
                            return [
                                'id' => $term->term_id,
                                'name' => $term->name,
                                'slug' => $term->slug,
                            ];
                        }, $terms);
                    } else {
                        $data[$taxonomy] = [];
                    }
                }

                return $data;
            },
            'schema' => null,
        ]);
    }
});

