<?php 

function crear_tabla_pagos() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'pagos_mp';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        payment_id VARCHAR(100),
        status VARCHAR(50),
        payer_email VARCHAR(100),
        payer_name VARCHAR(100),
        payer_surname VARCHAR(100),
        amount DECIMAL(10,2),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

?>