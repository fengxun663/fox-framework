<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Icon_Picker {

    public static function render_field($field) {
        $value = get_option($field['id'], $field['default'] ?? '');

        echo '<div class="fox-field">';
        echo '<label for="' . esc_attr($field['id']) . '">' . esc_html($field['title']) . '</label>';
        echo '<input type="text" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '" placeholder="选择一个图标">';
        echo '<button type="button" class="fox-icon-picker-button">选择图标</button>';
        echo '<div class="fox-icon-picker-container" style="display:none;">';
        foreach ($field['icons'] as $icon) {
            echo '<span class="fox-icon" data-icon="' . esc_attr($icon) . '"><i class="' . esc_attr($icon) . '"></i></span>';
        }
        echo '</div>';
        echo '</div>';
    }

    public static function enqueue_scripts() {
        wp_enqueue_script('fox-icon-picker', plugin_dir_url(__FILE__) . 'js/icon-picker.js', array('jquery'), '1.0', true);
        wp_enqueue_style('fox-icon-picker', plugin_dir_url(__FILE__) . 'css/icon-picker.css');
    }
}

add_action('admin_enqueue_scripts', array('Fox_Icon_Picker', 'enqueue_scripts'));