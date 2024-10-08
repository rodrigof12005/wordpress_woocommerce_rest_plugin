<?php

class Products_Route {
    
    private $consumer_key = 'your_consumer_key';
    private $consumer_secret = 'your_consumer_secret';

    public function register_routes() {
        register_rest_route('myplugin/v1', '/products', [
            'methods' => 'GET',
            'callback' => [$this, 'get_products'],
            'args' => [
                'paged' => [
                    'required' => false,
                    'default' => 1,
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ]
            ]
        ]);
    }

    public function get_products($request) {
        $paged = (int) $request->get_param('paged') ? (int) $request->get_param('paged') : 1;

        $url = add_query_arg([
            'page' => $paged,
            'per_page' => 5,
            'orderby' => 'price',
            'order' => 'asc'
        ], 'https://your_source_woocommerce_endpoint');

        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->consumer_key . ':' . $this->consumer_secret)
            ]
        ]);

        if (is_wp_error($response)) {
            return new WP_REST_Response(['error' => $response->get_error_message()], 500);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data)) {
            return new WP_REST_Response([], 200);
        }

        $products = array_map(function($product) {
            return [
                'id' => $product['id'],
                'title' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'type' => $product['type'],
                'imageUrl' => $product['images'][0]['src'] ?? '',
                'productLink' => $product['permalink']
            ];
        }, $data);

        return new WP_REST_Response($products, 200);
    }
}
