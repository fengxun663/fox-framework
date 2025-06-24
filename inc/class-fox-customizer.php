<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Customizer {
    public static function init() {
        add_action('customize_register', array(__CLASS__, 'customize_register'));
    }

    public static function customize_register($wp_customize) {
        $wp_customize->add_section('fox_customizer_section', array(
            'title' => '主题自定义器',
            'priority' => 30,
        ));

        $wp_customize->add_setting('fox_customizer_setting', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('fox_customizer_control', array(
            'label' => '自定义设置',
            'section' => 'fox_customizer_section',
            'settings' => 'fox_customizer_setting',
            'type' => 'text',
        ));
    }
}

Fox_Customizer::init();