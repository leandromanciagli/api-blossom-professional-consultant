<?php
/*
Plugin Name: API Blossom
Description: Plugin que registra CPTs, taxonomías y campos ACF para Blossom. REST endpoint para pagos.
Version: 1.0
Author: Manciagli Leandro
*/

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Post Types
require_once plugin_dir_path(__FILE__) . 'cpt/course.php';
require_once plugin_dir_path(__FILE__) . 'cpt/professional.php';

// Taxonomías
require_once plugin_dir_path(__FILE__) . 'taxonomies/topic.php';

// Campos ACF
require_once plugin_dir_path(__FILE__) . 'acf/fields-course.php';
require_once plugin_dir_path(__FILE__) . 'acf/fields-professional.php';

require_once plugin_dir_path(__FILE__) . 'rest-acf-expander.php';
require_once plugin_dir_path(__FILE__) . 'create-table-mp-paids.php';

// Utilidades
// require_once plugin_dir_path(__FILE__) . 'includes/utils.php';

require_once plugin_dir_path(__FILE__) . 'src/controller/MercadoPagoController.php';

register_activation_hook(__FILE__, 'crear_tabla_pagos');

add_action('rest_api_init', function () {

  register_rest_route('api', '/create-preference', [
    'methods'  => 'POST',
    'callback' => 'create_checkout_preference',
    'permission_callback' => '__return_true',
  ]);

  register_rest_route('api', '/payment-status', [
    'methods' => 'POST',
    'callback' => 'get_payment_status',
    'permission_callback' => '__return_true',
  ]);

  register_rest_route('api', '/webhook', [
    'methods'  => 'POST',
    'callback' => 'mp_webhook_callback',
    'permission_callback' => '__return_true',
  ]);
  
});

?>
