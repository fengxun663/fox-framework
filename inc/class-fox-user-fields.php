<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_User_Fields {
    public static function init() {
        add_action('show_user_profile', array(__CLASS__, 'show_user_fields'));
        add_action('edit_user_profile', array(__CLASS__, 'show_user_fields'));
        add_action('personal_options_update', array(__CLASS__, 'save_user_fields'));
        add_action('edit_user_profile_update', array(__CLASS__, 'save_user_fields'));
    }

    public static function show_user_fields($user) {
        $value = get_user_meta($user->ID, 'fox_user_field', true);
        echo '<h3>自定义用户字段</h3>';
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="fox_user_field">自定义字段</label></th>';
        echo '<td><input type="text" id="fox_user_field" name="fox_user_field" value="' . esc_attr($value) . '"></td>';
        echo '</tr>';
        echo '</table>';
    }

    public static function save_user_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        if (isset($_POST['fox_user_field'])) {
            update_user_meta($user_id, 'fox_user_field', sanitize_text_field($_POST['fox_user_field']));
        }
    }
}

Fox_User_Fields::init();