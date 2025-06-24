<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Framework {
    private static $options = array();

    public static function init() {
        // 注册选项
        add_action('admin_init', array(__CLASS__, 'register_options'));
        
    }

    public static function register_options() {
        $menu_slugs = array_keys(self::$options);
        foreach ($menu_slugs as $menu_slug) {
            register_setting($menu_slug, $menu_slug);
        }
    }

    public static function createSection($menu_slug, $args) {
        if (!isset(self::$options[$menu_slug])) {
            self::$options[$menu_slug] = array(
                'page_title' => $args['page_title'] ?? '主题设置', // 管理页面标题
                'sections'   => array()
            );
        }

        self::$options[$menu_slug]['sections'][] = $args;
    }

    public static function get_options() {
        return self::$options;
    }
	
    public function __construct() {
        add_action('init', [$this, 'check_license']);
    }

    public function check_license() {
        if (defined('FOX_FRAMEWORK_RESTRICTED') && FOX_FRAMEWORK_RESTRICTED) {
            // 移除 fox-framework 的后台菜单（阻止访问开发功能）
            add_action('admin_menu', function() {
                remove_menu_page('fox-framework-options');
            });

            // 禁止访问 fox-framework API
            add_action('rest_api_init', function() {
                unregister_rest_route('fox-framework/v1', '/options');
            });

            // 屏蔽框架的自定义字段（仅允许最终用户使用已开发的功能）
            remove_action('admin_enqueue_scripts', 'fox_framework_enqueue_scripts');
        }
    }	
}


