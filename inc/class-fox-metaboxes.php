<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Metaboxes {
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_boxes'));
    }

    public static function add_meta_boxes() {
        add_meta_box(
            'fox_post_metabox',
            '文章元框',
            array(__CLASS__, 'render_meta_box'),
            'post',
            'normal',
            'default'
        );
    }

    public static function render_meta_box($post) {
        $value = get_post_meta($post->ID, 'fox_metabox_field', true);
        wp_nonce_field('fox_metabox_nonce', 'fox_metabox_nonce');
        echo '<label for="fox_metabox_field">自定义字段</label>';
        echo '<input type="text" id="fox_metabox_field" name="fox_metabox_field" value="' . esc_attr($value) . '">';
    }

    public static function save_meta_boxes($post_id) {
        if (!isset($_POST['fox_metabox_nonce']) ||!wp_verify_nonce($_POST['fox_metabox_nonce'], 'fox_metabox_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['fox_metabox_field'])) {
            update_post_meta($post_id, 'fox_metabox_field', sanitize_text_field($_POST['fox_metabox_field']));
        }
    }
}

Fox_Metaboxes::init();