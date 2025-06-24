<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Admin {
    
    // 注册管理页面的 CSS 和 JS
    public static function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_fox_theme_options') {
            return;
        }

        // 加载 WordPress 内置的颜色选择器
        wp_enqueue_style('wp-color-picker');

        // 加载自定义 CSS
        wp_enqueue_style('fox-admin-css', get_template_directory_uri() . '/fox-framework/assets/admin.css');

        // 加载自定义 JS（并确保依赖项正确）
        wp_enqueue_script('fox-admin-js', get_template_directory_uri() . '/fox-framework/assets/admin.js', array('jquery', 'wp-color-picker', 'media-upload', 'thickbox'), false, true);
        wp_localize_script('fox-admin-js', 'fox_ajax', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));

        // 确保加载 TinyMCE 编辑器资源
        wp_enqueue_editor();
    }

    // 渲染管理页面
    public static function render_admin_page() {
        $menu_slug = $_GET['page'] ?? '';
        $menu_data = Fox_Framework::get_options()[$menu_slug] ?? [];

        if (empty($menu_data['sections'])) {
            echo '<div class="wrap"><h2>未找到任何设置项</h2></div>';
            return;
        }

        // 获取页面标题
        $page_title = $menu_data['page_title'] ?? '主题设置';

        echo '<div class="fox-wrap">';
        
        // 顶部标题
        echo '<div class="fox-header"><h1>' . esc_html($page_title) . '</h1></div>';

        // 主体内容
        echo '<div class="fox-main-container">';

        // 左侧菜单
        echo '<div class="fox-sidebar"><ul>';
        foreach ($menu_data['sections'] as $index => $section) {
            $active_class = $index === 0 ? ' class="active"' : '';
            echo '<li' . $active_class . ' data-section="section-' . esc_attr($index) . '">' . esc_html($section['title']) . '</li>';
        }
        echo '</ul></div>';

        // 右侧字段区域
        echo '<div class="fox-content">';
        foreach ($menu_data['sections'] as $index => $section) {
            $display_style = $index === 0 ? '' : ' style="display:none;"';
            echo '<div id="section-' . esc_attr($index) . '" class="fox-section"' . $display_style . '>';
            echo '<h2>' . esc_html($section['title']) . '</h2>';
            echo '<form method="post" action="options.php">';
            settings_fields($menu_slug);

            // 处理字段分组
            if (isset($section['groups'])) {
                foreach ($section['groups'] as $group) {
                    echo '<h3>' . esc_html($group['title']) . '</h3>';
                    foreach ($group['fields'] as $field) {
                        self::render_field($field, $menu_slug);
                    }
                }
            } else {
                foreach ($section['fields'] as $field) {
                    self::render_field($field, $menu_slug);
                }
            }

            submit_button();
            echo '</form>';
            echo '</div>';
        }
        echo '</div></div></div>'; // 结束 `fox-main-container` 和 `fox-wrap`
    }

    // 渲染字段
    public static function render_field($field, $menu_slug) {
        $option_name = $menu_slug . '[' . $field['id'] . ']';
        $value = get_option($menu_slug)[$field['id']] ?? $field['default'] ?? '';

        // 处理依赖关系
        $dependency_result = Fox_Dependency::handle_dependency($field, $menu_slug);
        $display_style = $dependency_result['display_style'];
        $dependency_attr = $dependency_result['dependency_attr'];

        echo '<div class="fox-field"' . $display_style . $dependency_attr . '>';
        echo '<label for="' . esc_attr($field['id']) . '">' . esc_html($field['title']) . '</label>';

        switch ($field['type']) {
            case 'text':
                echo '<input type="text" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '" />';
                break;

            case 'color':
                echo '<input type="text" class="fox-color-picker" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '" />';
                break;

            case 'media':
                echo '<div class="fox-media-container">';
                echo '<input type="text" id="' . esc_attr($field['id']) . '" class="fox-media-url" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '" />';
                echo '<button type="button" class="button fox-media-upload">上传</button>';
                echo '</div>';
                break;

            case 'rich_text_editor':
                Fox_Rich_Text_Editor::render_field($field);
                break;

            default:
                echo '<p>未知字段类型：' . esc_html($field['type']) . '</p>';
        }

        echo '</div>';
    }

    // AJAX 处理保存选项
    public static function save_options() {
        $menu_slug = $_POST['menu_slug'];
        $data = $_POST['data'];
        parse_str($data, $parsed_data);

        if (!empty($parsed_data[$menu_slug])) {
            update_option($menu_slug, $parsed_data[$menu_slug]);
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }
}

// 挂载资源
add_action('admin_enqueue_scripts', array('Fox_Admin', 'enqueue_assets'));

// 注册 AJAX 处理函数
add_action('wp_ajax_fox_save_options', array('Fox_Admin', 'save_options'));