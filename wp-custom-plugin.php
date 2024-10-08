<?php
/**
 * Plugin Name: APi Rest Woocommerce 
 * Description: Plugin personalizado para criar uma rota que lista produtos do WooCommerce.
 * Version: 1.0.0
 * Author: Rodrigo Duarte
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Inclui a classe de rota personalizada
require_once plugin_dir_path( __FILE__ ) . 'includes/Products_Route.php';

// Inicializa a rota personalizada
add_action('rest_api_init', function() {
    $products_route = new Products_Route();
    $products_route->register_routes();
});
