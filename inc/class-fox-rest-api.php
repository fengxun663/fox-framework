<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_REST_API {
    public static function init() {
        add_action('rest_api_init', array(__CLASS__, 'register_routes'));
    }

    public static function register_routes() {
        register_rest_route('fox/v1', '/options', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_options'),
        ));
    }

    public static function get_options() {
        $options = get_option('fox_options');
        return rest_ensure_response($options);
    }
}

Fox_REST_API::init();