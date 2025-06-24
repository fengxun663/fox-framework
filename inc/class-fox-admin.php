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
        
        // 确保加载 TinyMCE 编辑器资源
        wp_enqueue_editor();

        // 添加一个动作钩子，允许开发者在加载资源后执行自定义代码
        do_action('fox_admin_enqueue_assets_after');
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
        echo '<div class="fox-header"><h3>' . esc_html($page_title) . '</h3><span>V1.3</span></div>';

        // 触发一个动作钩子，允许开发者在标题后插入内容
        do_action('fox_admin_page_title_after');

        // 主体内容
        echo '<div class="fox-main-container">';

        // 左侧菜单
// 左侧菜单
echo '<div class="fox-sidebar"><ul>';

// 初始化 $current_section 变量，防止未定义变量错误
$current_section = isset($current_section) ? $current_section : '';

// 确保 $menu_data['sections'] 存在且是数组
if (isset($menu_data['sections']) && is_array($menu_data['sections'])) {
    foreach ($menu_data['sections'] as $index => $section) {
        // 检查 'id' 和 'title' 键是否存在
        $section_id = isset($section['id']) ? $section['id'] : '';
        $section_title = isset($section['title']) ? $section['title'] : '';
        
        // 生成 active 类
        $active_class = $current_section === $section_id ? ' class="active"' : '';
        
        // 处理图标
        $icon = '';
        if (isset($section['icon']) && !empty($section['icon'])) {
            $icon = '<i class="dashicons ' . esc_attr($section['icon']) . '"></i> ';
        }
        
        // 输出菜单项，只在 ID 存在时才添加 data-section 属性
        echo '<li' . $active_class;
        if (!empty($section_id)) {
            echo ' data-section="' . esc_attr($section_id) . '"';
        }
        echo '>';
        echo $icon . esc_html($section_title);
        echo '</li>';
    }
}
echo '</ul></div>';

        // 右侧字段区域
        echo '<div class="fox-content">';
        foreach ($menu_data['sections'] as $index => $section) {
            $display_style = $index === 0 ? '' : ' style="display:none;"';
            echo '<div id="section-' . esc_attr($index) . '" class="fox-section"' . $display_style . '>';
        //    echo '<h2>' . esc_html($section['title']) . '</h2>';

// 在 render_admin_page 方法中修改字段组渲染逻辑
if (isset($section['groups']) && !empty($section['groups'])) {
    echo '<div class="fox-tabs">';
    echo '<ul class="fox-tabs-nav">';
    foreach ($section['groups'] as $group_index => $group) {
        $active_class = $group_index === 0 ? ' class="active"' : '';
        echo '<li' . $active_class . ' data-tab="tab-' . esc_attr($index) . '-' . esc_attr($group_index) . '">';
        if (!empty($group['icon'])) {
            echo '<i class="dashicons ' . esc_attr($group['icon']) . '"></i> ';
        }
        echo esc_html($group['title']);
        echo '</li>';
    }
    echo '</ul>';

    // 所有字段放在同一个表单中
    echo '<form method="post" action="options.php">';
    settings_fields($menu_slug);
    
    // 渲染选项卡内容
    echo '<div class="fox-tabs-content">';
    foreach ($section['groups'] as $group_index => $group) {
        $tab_display_style = $group_index === 0 ? '' : ' style="display:none;"';
        echo '<div id="tab-' . esc_attr($index) . '-' . esc_attr($group_index) . '" class="fox-tab-content"' . $tab_display_style . '>';
        
        // 渲染字段组描述（如果有）
        if (!empty($group['desc'])) {
            echo '<div class="fox-field-desc">' . esc_html($group['desc']) . '</div>';
        }
        
        // 渲染字段
        foreach ($group['fields'] as $field) {
            self::render_field($field, $menu_slug);
        }
        
        echo '</div>';
    }
    
    // 提交按钮移到表单底部
    submit_button();
    echo '</form>';
    echo '</div>';
    echo '</div>';
} else {
    // 没有字段组，保持原有逻辑
    echo '<form method="post" action="options.php">';
    settings_fields($menu_slug);
    
    foreach ($section['fields'] as $field) {
        self::render_field($field, $menu_slug);
    }
    
    submit_button();
    echo '</form>';
}
            
            echo '</div>';
        }
        echo '</div></div></div>'; // 结束 `fox-main-container` 和 `fox-wrap`

        // 在 render_admin_page 方法的末尾添加
?>
<script>
jQuery(document).ready(function($) {
    // 初始化选项卡
    function initTabs() {
        // 确保每个选项卡组的第一个标签是激活的
        $('.fox-tabs').each(function() {
            var $tabs = $(this);
            $tabs.find('.fox-tabs-nav li:first').addClass('active');
            $tabs.find('.fox-tab-content:first').show().addClass('active');
        });
        
        // 绑定选项卡切换事件
        $('.fox-tabs-nav li').on('click', function() {
            var tab_id = $(this).data('tab');
            
            // 切换导航项的激活状态
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            
            // 切换内容的显示状态
            $(this).closest('.fox-tabs').find('.fox-tab-content').hide();
            $('#' + tab_id).show();
        });
    }
    
    // 初始调用
    initTabs();
    
    // 监听左侧菜单点击事件
    $('.fox-sidebar li').on('click', function() {
        // 等待内容加载完成
        setTimeout(function() {
            // 重置所有选项卡状态
            $('.fox-tabs-nav li').removeClass('active');
            $('.fox-tab-content').hide();
            
            // 重新初始化
            initTabs();
        }, 300);
    });
});
</script>
<?php        

        // 触发一个动作钩子，允许开发者在页面底部插入内容
        do_action('fox_admin_page_end');
    }

    // 渲染字段
    public static function render_field($field, $menu_slug) {
        $option_name = $menu_slug . '[' . $field['id'] . ']';
        $value = get_option($menu_slug)[$field['id']] ?? $field['default'] ?? '';

        // 触发一个过滤器钩子，允许开发者修改字段数据
        $field = apply_filters('fox_admin_render_field_data', $field, $menu_slug);

        // 处理依赖关系
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

        echo '<div class="fox-field"' . $display_style . $dependency_attr . '>';
        echo '<label for="' . esc_attr($field['id']) . '">' . esc_html($field['title']) . '</label>';
		
		// 显示说明文字
		if (isset($field['desc'])) {
			echo '<p class="fox-field-desc">' . esc_html($field['desc']) . '</p>';
		}		

        switch ($field['type']) {
			// 单行文本
            case 'text':
                echo '<input type="text" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '" />';
                break;
			// 多行文本
            case 'textarea':
                echo '<textarea id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder'] ?? '') . '" style="height: 200px; width:600px">' . esc_textarea($value) . '</textarea>';
                break;
			// 开关
            case 'switch':
                $checked = !empty($value) ? 'checked' : '';
                echo '<label class="fox-switch">';
                echo '<input type="checkbox" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="1" ' . $checked . '>';
                echo '<span class="fox-slider"></span>';
                echo '</label>';
                break;
			// 数字
            case 'number':
                $min = $field['min'] ?? '';
                $max = $field['max'] ?? '';
                $step = $field['step'] ?? '';
                echo '<input type="number" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '" min="' . esc_attr($min) . '" max="' . esc_attr($max) . '" step="' . esc_attr($step) . '">';
                break;
			// 多媒体
            case 'media':
                echo '<div class="fox-media-container">';
                echo '<img src="' . esc_url($value) . '" class="fox-media-preview" style="max-width: 100px; margin: 5px;">';
                echo '<input type="text" id="' . esc_attr($field['id']) . '" class="fox-media-url" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '" />';
                echo '<button type="button" class="button fox-media-upload">上传</button>';

                echo '</div>';
                break;
			// 颜色
            case 'color':
                echo '<input type="text" class="fox-color-picker" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '" />';
                break;
			// 图片
            case 'image':
                echo '<div class="fox-media-wrapper">';
                echo '<input type="hidden" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '">';

                echo '<img src="' . esc_url($value) . '" class="fox-media-preview" style="max-width: 100px; display: ' . ($value ? 'inline-block' : 'none') . ';">';
                echo '<button type="button" class="button fox-media-upload">上传图片</button>';                
                echo '<button type="button" class="button fox-media-remove" style="display: ' . ($value ? 'inline-block' : 'none') . ';">移除</button>';
                echo '</div>';
                break;
            // 图集
            case 'gallery':
                echo '<div class="fox-gallery-wrapper">';
                echo '<input type="hidden" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '">';
                echo '<p>点击+号添加一个图片，点-号删除一个图片，图片大小</p>';				
                echo '<button type="button" class="button fox-gallery-add">+</button>';
                echo '<div class="fox-gallery-items">';
                $images = explode(',', $value);
                foreach ($images as $index => $image) {
                    echo '<div class="fox-gallery-item">';
                    echo '<input type="text" class="fox-gallery-url" value="' . esc_url($image) . '">';
                    // 为已有图片项添加上传按钮
                    echo '<button type="button" class="button fox-gallery-upload">上传图片</button>';
                    echo '<button type="button" class="button fox-gallery-remove">-</button>';
                    echo '<img src="' . esc_url($image) . '" class="fox-gallery-preview" style="max-width: 100px; margin: 5px;">';
                    echo '</div>';
                }
                echo '</div>';
                echo '</div>';
                break;

            case 'repeater':
                echo '<div class="fox-repeater">';
                echo '<button type="button" class="button fox-repeater-add">添加项</button>';
                $items = maybe_unserialize($value);
                if (is_array($items)) {
                    foreach ($items as $index => $item) {
                        echo '<div class="fox-repeater-item">';
                        foreach ($field['fields'] as $sub_field) {
                            $sub_field['id'] = $field['id'] . '[' . $index . '][' . $sub_field['id'] . ']';
                            $sub_field['default'] = $item[$sub_field['id']] ?? $sub_field['default'] ?? '';
                            self::render_field($sub_field, $menu_slug);
                        }
                        echo '<button type="button" class="button fox-repeater-remove">移除项</button>';
                        echo '</div>';
                    }
                }
                echo '</div>';
                break;
			// 字段组
            case 'group':
                echo '<div class="fox-group" style="width:10%;">';
                foreach ($field['fields'] as $sub_field) {
                    self::render_field($sub_field, $menu_slug);
                }
                echo '</div>';
                break;
			// 日期
            case 'date':
                echo '<input type="date" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '">';
                break;
			//下拉框
			case 'select':
				echo '<select id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '">';
				foreach ($field['options'] as $option_value => $option_label) {
					$selected = ($value === $option_value) ? 'selected' : '';
					echo '<option value="' . esc_attr($option_value) . '" ' . $selected . '>' . esc_html($option_label) . '</option>';
				}
				echo '</select>';
				break;			
			// 手风琴
            case 'accordion':
                echo '<div class="fox-accordion">';
                foreach ($field['sections'] as $section) {
                    echo '<div class="fox-accordion-section">';
                    echo '<h3 class="fox-accordion-title">' . esc_html($section['title']) . '</h3>';
                    echo '<div class="fox-accordion-content">';
                    foreach ($section['fields'] as $sub_field) {
                        self::render_field($sub_field, $menu_slug);
                    }
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
                break;
			// 富文本
            case 'rich_text_editor':
                Fox_Rich_Text_Editor::render_field($field);
                break;

            // 单选框
            case 'radio':
                $layout = $field['layout'] ?? 'vertical'; // 默认竖式排列
                $use_images = !empty($field['options_images']); // 是否使用图片选项
                $use_icons = !empty($field['options_icons']); // 是否使用图标选项
                $show_labels = $use_images || $use_icons ? ($field['show_labels'] ?? false) : true; // 是否显示文字标签
                $class = ($layout === 'horizontal') ? 'fox-radio-group-horizontal' : 'fox-radio-group-vertical';
                echo '<div class="' . $class . '">';
                
                foreach ($field['options'] as $option_value => $option_label) {
                    $checked = ($value === $option_value) ? 'checked' : '';
                    $image_url = $use_images ? ($field['options_images'][$option_value] ?? '') : '';
                    $icon_class = $use_icons ? ($field['options_icons'][$option_value] ?? '') : '';
                    
                    echo '<label class="fox-radio-label' . (($use_images || $use_icons) ? ' fox-radio-image-label' : '') . '">';
                    echo '<input type="radio" id="' . esc_attr($field['id']) . '-' . esc_attr($option_value) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($option_value) . '" ' . $checked . '>';
                    
                    if ($use_images || $use_icons) {
                        echo '<span class="fox-radio-image-container">';
                        if ($use_images) {
                            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($option_label) . '" class="fox-radio-image">';
                        } elseif ($use_icons) {
                            echo '<i class="' . esc_attr($icon_class) . ' fox-radio-icon"></i>';
                        }
                        echo '<span class="fox-radio-image-check"><i class="dashicons dashicons-yes"></i></span>';
                        echo '</span>';
                        if ($show_labels) {
                            echo '<span class="fox-radio-image-label-text">' . esc_html($option_label) . '</span>';
                        }
                    } else {
                        echo '<span class="fox-radio-custom"></span>';
                        echo esc_html($option_label);
                    }
                    
                    echo '</label>';
                }
                
                echo '</div>';
                break;

            // 复选框
            case 'checkbox':
                $layout = $field['layout'] ?? 'vertical'; // 默认竖式排列
                $use_images = !empty($field['options_images']); // 是否使用图片选项
                $use_icons = !empty($field['options_icons']); // 是否使用图标选项
                $show_labels = $use_images || $use_icons ? ($field['show_labels'] ?? false) : true; // 是否显示文字标签
                $class = ($layout === 'horizontal') ? 'fox-checkbox-group-horizontal' : 'fox-checkbox-group-vertical';
                $values = is_array($value) ? $value : explode(',', $value);
                
                echo '<div class="' . $class . '">';
                
                foreach ($field['options'] as $option_value => $option_label) {
                    $checked = in_array($option_value, $values) ? 'checked' : '';
                    $image_url = $use_images ? ($field['options_images'][$option_value] ?? '') : '';
                    $icon_class = $use_icons ? ($field['options_icons'][$option_value] ?? '') : '';
                    
                    echo '<label class="fox-checkbox-label' . (($use_images || $use_icons) ? ' fox-checkbox-image-label' : '') . '">';
                    if ($use_images || $use_icons) {
                        // 隐藏原生复选框，使用图片或图标替代
                        echo '<input type="checkbox" id="' . esc_attr($field['id']) . '-' . esc_attr($option_value) . '" name="' . esc_attr($option_name) . '[]" value="' . esc_attr($option_value) . '" ' . $checked . ' class="sr-only">';
                        echo '<span class="fox-checkbox-image-container">';
                        if ($use_images) {
                            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($option_label) . '" class="fox-checkbox-image">';
                        } elseif ($use_icons) {
                            echo '<i class="' . esc_attr($icon_class) . ' fox-checkbox-icon"></i>';
                        }
                        echo '<span class="fox-checkbox-image-check"><i class="dashicons dashicons-yes"></i></span>';
                        echo '</span>';
                        if ($show_labels) {
                            echo '<span class="fox-checkbox-image-label-text">' . esc_html($option_label) . '</span>';
                        }
                    } else {
                        // 传统复选框显示方式
                        echo '<input type="checkbox" id="' . esc_attr($field['id']) . '-' . esc_attr($option_value) . '" name="' . esc_attr($option_name) . '[]" value="' . esc_attr($option_value) . '" ' . $checked . '>';
                        echo esc_html($option_label);
                    }
                    
                    echo '</label>';
                }
                
                echo '</div>';
                break;

            // 分类选择字段
            case 'category_select': // 下拉选择
                $categories = get_categories(array(
                    'hide_empty' => false // 显示所有分类，包括没有文章的分类
                ));
                echo '<select id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '">';
                foreach ($categories as $category) {
                    $selected = ($value == $category->term_id) ? 'selected' : '';
                    echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                }
                echo '</select>';
                break;

            // 分类选择字段 - 单选框
            case 'category_radio':
                $layout = $field['layout'] ?? 'vertical'; // 默认竖式排列
                $use_images = !empty($field['options_images']); // 是否使用图片选项
                $use_icons = !empty($field['options_icons']); // 是否使用图标选项
                $show_labels = $use_images || $use_icons ? ($field['show_labels'] ?? false) : true; // 是否显示文字标签
                $class = ($layout === 'horizontal') ? 'fox-radio-group-horizontal' : 'fox-radio-group-vertical';
                echo '<div class="' . $class . '">';
    
                // 动态获取分类
                $categories = get_categories(array(
                    'hide_empty' => false, // 显示所有分类，包括没有文章的分类
                ));
                foreach ($categories as $category) {
                    $option_value = $category->term_id;
                    $option_label = $category->name;
                    $checked = ($value == $option_value) ? 'checked' : '';
                    $image_url = $use_images ? ($field['options_images'][$option_value] ?? '') : '';
                    $icon_class = $use_icons ? ($field['options_icons'][$option_value] ?? '') : '';
    
                    echo '<label class="fox-radio-label' . (($use_images || $use_icons) ? ' fox-radio-image-label' : '') . '">';
                    echo '<input type="radio" id="' . esc_attr($field['id']) . '-' . esc_attr($option_value) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($option_value) . '" ' . $checked . '>';
    
                    if ($use_images || $use_icons) {
                        echo '<span class="fox-radio-image-container">';
                        if ($use_images) {
                            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($option_label) . '" class="fox-radio-image">';
                        } elseif ($use_icons) {
                            echo '<i class="' . esc_attr($icon_class) . ' fox-radio-icon"></i>';
                        }
                        echo '<span class="fox-radio-image-check"><i class="dashicons dashicons-yes"></i></span>';
                        echo '</span>';
                        if ($show_labels) {
                            echo '<span class="fox-radio-image-label-text">' . esc_html($option_label) . '</span>';
                        }
                    } else {
                        echo '<span class="fox-radio-custom"></span>';
                        echo esc_html($option_label);
                    }
    
                    echo '</label>';
                }
    
                echo '</div>';
                break;  

            default:
                echo '<p>未知字段类型：' . esc_html($field['type']) . '</p>';
        }

        echo '</div>';
    }

    // AJAX 处理保存选项
    public static function save_options() {
        $menu_slug = $_POST['menu_slug'];
        $new_data = array();

        // 解析提交的数据
        parse_str($_POST['data'], $new_data);

        // 获取现有的选项数据
        $existing_options = get_option($menu_slug);

        // 合并新数据和现有数据
        $updated_options = array_merge($existing_options, $new_data[$menu_slug]);

        // 更新选项
        update_option($menu_slug, $updated_options);

        wp_send_json_success(array(
            'message' => '保存成功'
        ));
    }
}

// 挂载资源
add_action('admin_enqueue_scripts', array('Fox_Admin', 'enqueue_assets'));
// 注册 AJAX 处理函数
add_action('wp_ajax_fox_save_options', array('Fox_Admin', 'save_options'));