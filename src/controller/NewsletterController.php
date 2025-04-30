<?php

function create_newsletter_subscriptor($request) {
    try {
        $email = sanitize_email($request->get_param('email'));

        if (empty($email) || !is_email($email)) {
            return new WP_Error('invalid_email', 'Email inválido.', array('status' => 400));
        }

        // Configuración de autorización
        $client_key = NEWSLETTER_USERNAME;
        $client_secret = NEWSLETTER_PASSWORD;
        $auth = base64_encode("$client_key:$client_secret");

        // Llamada a la API del plugin Newsletter
        $response = wp_remote_post(rest_url('/newsletter/v2/subscriptions'), array(
            'timeout' => 15,
            'headers' => array(
                'Authorization' => 'Basic ' . $auth,
                'Content-Type'  => 'application/json',
            ),
            'body' => json_encode(array(
                'email' => $email,
                'optin' => 'single',
            )),
        ));

        if (is_wp_error($response)) {
            return new WP_Error('request_failed', 'Error al contactar el servicio.', array('status' => 500));
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        return rest_ensure_response(array(
            'status' => $code,
            'response' => json_decode($body, true),
        ));
    } catch (\Exception $e) {
        return new WP_REST_Response(['error' => $e->getMessage()], $e->getStatusCode());
    }
}