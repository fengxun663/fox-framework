<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Taxonomy_Fields {
    public static function init() {
        add_action('category_add_form_fields', array(__CLASS__, 'add_category_fields'));
        add_action('category_edit_form_fields', array(__CLASS__, 'edit_category_fields'));
        add_action('created_category', array(__CLASS__, 'save_category_fields'));
        add_action('edited_category', array(__CLASS__, 'save_category_fields'));
    }

    public static function add_category_fields() {
        echo '<div class="form-field">';
        echo '<label for="fox_category_field">自定义分类字段</label>';
        echo '<input type="text" id="fox_category_field" name="fox_category_field">';
        echo '<p class="description">这是一个自定义分类字段</p>';
        echo '</div>';
    }

    public static function edit_category_fields($term) {
        $value = get_term_meta($term->term_id, 'fox_category_field', true);
        echo '<tr class="form-field">';
        echo '<th scope="row"><label for="fox_category_field">自定义分类字段</label></th>';
        echo '<td><input type="text" id="fox_category_field" name="fox_category_field" value="' . esc_attr($value) . '">';
        echo '<p class="description">这是一个自定义分类字段</p></td>';
        echo '</tr>';
    }

    public static function save_category_fields($term_id) {
        if (isset($_POST['fox_category_field'])) {
            update_term_meta($term_id, 'fox_category_field', sanitize_text_field($_POST['fox_category_field']));
        }
    }
}

Fox_Taxonomy_Fields::init();