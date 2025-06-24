<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Rich_Text_Editor {

    public static function render_field($field) {
        $value = get_option($field['id'], $field['default'] ?? '');

        // 定义自定义的按钮集
        $buttons = 'bold,italic,underline,strikethrough,forecolor,backcolor,fontsizeselect,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,link,unlink,removeformat';

        echo '<div class="fox-field">';
        echo '<label for="' . esc_attr($field['id']) . '">' . esc_html($field['title']) . '</label>';
        wp_editor(
            $value,
            $field['id'],
            array(
                'textarea_name' => $field['id'],
                'textarea_rows' => $field['rows'] ?? 10,
                'teeny'         => $field['teeny'] ?? false,
                'media_buttons' => $field['media_buttons'] ?? true,
                'tinymce'       => array(
                    'toolbar1' => $buttons,
                ),
            )
        );
        echo '</div>';
    }

    public static function enqueue_scripts() {
        // 富文本编辑器所需的脚本和样式会由 wp_editor 自动加载
    }
}

add_action('admin_enqueue_scripts', array('Fox_Rich_Text_Editor', 'enqueue_scripts'));