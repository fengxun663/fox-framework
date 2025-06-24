<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Font_Manager {

    public static function render_field($field) {
        $value = get_option($field['id'], $field['default'] ?? '');

        echo '<div class="fox-field">';
        echo '<label for="' . esc_attr($field['id']) . '">' . esc_html($field['title']) . '</label>';
        echo '<select id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '">';
        foreach ($field['fonts'] as $font) {
            $selected = ($value === $font) ? 'selected' : '';
            echo '<option value="' . esc_attr($font) . '" ' . $selected . '>' . esc_html($font) . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }

    public static function enqueue_scripts() {
        // 如果需要加载特定的字体相关的 CSS 或 JS，可以在这里添加
    }
}

add_action('admin_enqueue_scripts', array('Fox_Font_Manager', 'enqueue_scripts'));