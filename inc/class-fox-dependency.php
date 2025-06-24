<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Dependency {
    public static function handle_dependency($field, $menu_slug) {
        $dependency = $field['dependency'] ?? false;
        $display_style = '';
        $dependency_attr = '';

        if ($dependency) {
            $dependency_field = $dependency['field'];
            $dependency_value = $dependency['value'];
            $dependency_field_value = get_option($menu_slug)[$dependency_field] ?? '';

            if ($dependency_field_value != $dependency_value) {
                $display_style = ' style="display: none;"';
            }
            $dependency_attr = ' data-dependency="' . esc_attr($dependency_field . ':' . $dependency_value) . '"';
        }

        return array(
            'display_style' => $display_style,
            'dependency_attr' => $dependency_attr
        );
    }
}