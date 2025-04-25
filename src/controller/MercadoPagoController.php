<?php

use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;


function create_checkout_preference($request) {

    $params = $request->get_json_params();

    if (!isset($params['items'])) {
        return new WP_Error('missing_total', 'La compra debe poseer al menos un item', ['status' => 400]);
    }

    // Step 2: Set the production or sandbox access token
    // Getting and set the access token from .env file (create your own function)
    MercadoPagoConfig::setAccessToken(MP_ACCESS_TOKEN);

    // Step 2.1 (optional): Define the runtime environment
    // (Optional) Set the runtime enviroment to LOCAL if you want to test on localhost
    // Default value is set to SERVER
    MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

    // Step 3: Initialize the API client
    $client = new PreferenceClient();

    try {
        $items = [];
        foreach ($params['items'] as $item) {
            $items[] = [
                'title' => $item['title'],
                'unit_price' => floatval($item['price']),
                'currency_id' => 'ARS',
                'quantity' => 1,
            ];
        }

        // Step 4: Create the request array
        $request = [
            "items" => $items,
            "back_urls" => [
                "success" => FRONTEND_DOMAIN.'/home/success',
                "pending" => FRONTEND_DOMAIN.'/home/pending',
                "failure" => FRONTEND_DOMAIN.'/home/failure',
            ],
            "payer" => [
                "name" => $params['payer']['name'],
                "surname" => $params['payer']['surname'],
                "email" => $params['payer']['email'],
            ],
            "metadata" => [
                "name" => $params['payer']['name'],
                "surname" => $params['payer']['surname'],
                "email" => $params['payer']['email'],
            ],
            "auto_return" => "approved",
            "notification_url" => SERVER_DOMAIN."/wp-json/api/webhook",
        ];

        // Step 5: Create the request options, setting X-Idempotency-Key
        $request_options = new RequestOptions();
        $request_options->setCustomHeaders(["X-Idempotency-Key: <UNIQUE_KEY>"]);

        // Step 6: Make the request
        $preference = $client->create($request, $request_options);
        
        return rest_ensure_response([
            'id' => $preference->id,
            'init_point' => $preference->init_point,
        ]);

        // Step 7: Handle exceptions
    } catch (MPApiException $e) {
        return new WP_REST_Response(['error' => $e->getApiResponse()->getContent()], $e->getApiResponse()->getStatusCode());
    } catch (\Exception $e) {
        return new WP_REST_Response(['error' => $e->getMessage()], $e->getStatusCode());
    }
}

function get_payment_status($request) {
    try {
        $params = $request->get_json_params();
        $paymentId = $request['paymentId'];
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM 'wp_pagos_mp' WHERE payment_id = %d", $paymentId));
        return rest_ensure_response([
            'status' => $row ? $row->status : 'pending',
        ]);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}

function mp_webhook_callback(WP_REST_Request $request) {
    try {
        $body = $request->get_json_params();

        // Validar tipo de evento
        if (!isset($body['type']) || $body['type'] !== 'payment') {
            return new WP_REST_Response(['error' => 'Tipo de evento no soportado'], 400);
        }

        $payment_id = $body['data']['id'] ?? null;
        if (!$payment_id) {
            return new WP_REST_Response(['error' => 'ID de pago no encontrado'], 400);
        }

        // Consultar el pago a la API de Mercado Pago
        $access_token = MP_ACCESS_TOKEN;
        $url = "https://api.mercadopago.com/v1/payments/$payment_id?access_token=$access_token";
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return new WP_REST_Response(['error' => 'Error al consultar Mercado Pago'], 500);
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($data['status']) || $data['status'] !== 'approved') {
            return new WP_REST_Response(['status' => $data['status'] ?? 'unknown'], $data['status']);
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'pagos_mp';

        // Verificar si el pago ya está en la base de datos
        $existing = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE payment_id = %d", $payment_id));
        if ($existing > 0) {
            return new WP_REST_Response(['message' => 'Pago ya registrado'], 200);
        }

        // Insertar en la base de datos
        $wpdb->insert($table, [
            'payment_id'   => $data['id'],
            'status'       => $data['status'],
            'payer_name'  => $data['metadata']['name'],
            'payer_surname'  => $data['metadata']['surname'],
            'payer_email'  => $data['metadata']['email'],
            'amount'       => $data['transaction_amount'],
        ]);

        // Obtener el registro recién insertado
        $inserted_id = $wpdb->insert_id;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $inserted_id));

        $to = ADMIN_EMAIL;
        $subject = 'Blossom - Nuevo pago recibido';

        $emailBody = '
            <h2>¡Felicidades, recibiste una nueva compra!</h2>
            <h2>Datos de la compra</h2>
            <p><strong>Nombre:</strong> '.$row->payer_name.' '.$row->payer_surname.'</p>
            <p><strong>Email:</strong> '.$row->payer_email.'</p>
            <h2>Detalle de la compra:</h2>
            <ul>';
        foreach ($data['additional_info']['items'] as $item) {
            $emailBody = $emailBody . '<li>Curso: '.$item['title'].' $'.$item['unit_price'].'</li>';
        }
        
        $emailBody = $emailBody.'</ul>
            <p><strong>Total:</strong> $'.$row->amount.'</p>
            <p><strong>Importante: Se debe habilitar el contenido correspondiente al email del comprador y notificar al mismo.</strong></p>
        ';
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        
        wp_mail($to, $subject, $emailBody, $headers);

        return rest_ensure_response($row);

    } catch (Exception $e) {
        return new WP_REST_Response(['error' => 'Excepción: ' . $e->getMessage()], $e->getStatusCode());
    }
}




