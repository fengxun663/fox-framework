<?php
/**
 * Plugin Name: Fox Framework
 * Description: 主题选项框架，提供主题设置 UI 和 API。
 * Version: 1.0
 * Author: 你的名字
 */

if (!defined('ABSPATH')) {
    exit; // 禁止直接访问
}

// 加载核心文件
require_once plugin_dir_path(__FILE__) . 'inc/class-fox-framework.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-fox-admin.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-fox-metaboxes.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-fox-taxonomy-fields.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-fox-user-fields.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-fox-customizer.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-fox-widgets.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-fox-rest-api.php';

require_once plugin_dir_path(__FILE__) . 'fields/class-fox-icon-picker.php';
require_once plugin_dir_path(__FILE__) . 'fields/class-fox-font-manager.php';
require_once plugin_dir_path(__FILE__) . 'fields/class-fox-rich-text-editor.php';
require_once plugin_dir_path(__FILE__) . 'fields/class-fox-date-picker.php';


// 初始化框架
add_action('init', array('Fox_Framework', 'init'));

// 菜单管理自定义字段相关代码
// 添加自定义字段
add_filter('wp_setup_nav_menu_item', 'fox_add_nav_menu_custom_fields');
function fox_add_nav_menu_custom_fields($menu_item) {
    $menu_item->fox_custom_nav_field = get_post_meta($menu_item->ID, '_fox_nav_menu_custom_field', true);
    return $menu_item;
}

// 在 fox-framework.php 文件中添加以下代码

// 定义一个函数来设置全局变量
function fox_set_global_options() {
    global $fox_framework_options;
    $menu_slug = 'fox_theme_options';
    $options = get_option($menu_slug);
    if ($options) {
        foreach ($options as $field_id => $value) {
            $fox_framework_options[$field_id] = $value;
        }
    }
}
add_action('init', 'fox_set_global_options');

// 显示自定义字段
add_action('wp_nav_menu_item_custom_fields', 'fox_display_nav_menu_custom_fields', 10, 4);
function fox_display_nav_menu_custom_fields($item_id, $item, $depth, $args) {
    $custom_field_value = get_post_meta($item_id, '_fox_nav_menu_custom_field', true);
    echo '<p class="field-custom description description-wide">';
    echo '<label for="edit-menu-item-custom-' . $item_id . '">';
    echo '自定义菜单字段<br>';
    echo '<input type="text" id="edit-menu-item-custom-' . $item_id . '" class="widefat edit-menu-item-custom" name="menu-item-custom[' . $item_id . ']" value="' . esc_attr($custom_field_value) . '">';
    echo '</label>';
    echo '</p>';
}

// 保存自定义字段的值
add_action('wp_update_nav_menu_item', 'fox_save_nav_menu_custom_fields', 10, 2);
function fox_save_nav_menu_custom_fields($menu_id, $menu_item_db_id) {
    if (isset($_POST['menu-item-custom'][$menu_item_db_id])) {
        update_post_meta(
            $menu_item_db_id,
            '_fox_nav_menu_custom_field',
            sanitize_text_field($_POST['menu-item-custom'][$menu_item_db_id])
        );
    }
}
