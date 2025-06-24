<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Fields {

    public static function render_field($field) {
        $value = get_option($field['id'], $field['default'] ?? '');

        echo '<div class="fox-field">';
        echo '<label for="' . esc_attr($field['id']) . '">' . esc_html($field['title']) . '</label>';

        switch ($field['type']) {
            case 'text':
                echo '<input type="text" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($field['placeholder'] ?? '') . '">';
                break;

            case 'textarea':
                echo '<textarea id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" placeholder="' . esc_attr($field['placeholder'] ?? '') . '" style="height: 200px; width:600px">' . esc_textarea($value) . '</textarea>';
                break;

            case 'switch':
                $checked = !empty($value) ? 'checked' : '';
                echo '<label class="fox-switch">';
                echo '<input type="checkbox" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="1" ' . $checked . '>';
                echo '<span class="fox-slider"></span>';
                echo '</label>';
                break;

            case 'number':
                $min = $field['min'] ?? '';
                $max = $field['max'] ?? '';
                $step = $field['step'] ?? '';
                echo '<input type="number" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '" min="' . esc_attr($min) . '" max="' . esc_attr($max) . '" step="' . esc_attr($step) . '">';
                break;

            case 'media':
                echo '<div class="fox-media-wrapper">';
                echo '<input type="hidden" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '">';
                echo '<button type="button" class="button fox-media-upload">上传图片</button>';
                echo '<img src="' . esc_url($value) . '" class="fox-media-preview" style="max-width: 100px; display: ' . ($value ? 'inline-block' : 'none') . ';">';
                echo '<button type="button" class="button fox-media-remove" style="display: ' . ($value ? 'inline-block' : 'none') . ';">移除</button>';
                echo '</div>';
                break;

            case 'color':
                echo '<input type="text" class="fox-color-picker" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '">';
                break;

            case 'image':
                echo '<div class="fox-media-wrapper">';
                echo '<input type="hidden" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '">';
      
                echo '<img src="' . esc_url($value) . '" class="fox-media-preview" style="max-width: 100px; display: ' . ($value ? 'inline-block' : 'none') . ';">';
                echo '<button type="button" class="button fox-media-upload">上传</button>';
                echo '<button type="button" class="button fox-media-remove" style="display: ' . ($value ? 'inline-block' : 'none') . ';">删除</button>';
                echo '</div>';
                break;

			// 图集
			case 'gallery':
				$value = get_option($field['id'], $field['default'] ?? '');
				$images = explode(',', $value);

				echo '<div class="fox-gallery-wrapper">';
				echo '<input type="hidden" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '">';
				echo '<div class="fox-gallery-items">';
				foreach ($images as $index => $image) {
					echo '<div class="fox-gallery-item">';
					echo '<input type="text" class="fox-gallery-url" value="' . esc_url($image) . '">';
					// 为已有图片项添加上传按钮
					echo '<button type="button" class="button fox-gallery-upload">上传图片</button>';
					if ($index === count($images) - 1) {
						echo '<button type="button" class="button fox-gallery-add">+</button>';
					}
					if ($index > 0) {
						echo '<button type="button" class="button fox-gallery-remove">-</button>';
					}
					echo '<img src="' . esc_url($image) . '" class="fox-gallery-preview" style="max-width: 100px; margin: 5px;">';
					echo '</div>';
				}
				// 如果没有图片，添加一个空的图片项
				if (empty($images) || $images[0] === '') {
					echo '<div class="fox-gallery-item">';
					echo '<input type="text" class="fox-gallery-url" value="">';
					echo '<button type="button" class="button fox-gallery-upload">上传图片</button>';
					echo '<button type="button" class="button fox-gallery-add">+</button>';
					echo '<img src="" class="fox-gallery-preview" style="max-width: 100px; margin: 5px; display: none;">';
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
                            self::render_field($sub_field);
                        }
                        echo '<button type="button" class="button fox-repeater-remove">移除项</button>';
                        echo '</div>';
                    }
                }
                echo '</div>';
                break;

            case 'group':
                echo '<div class="fox-group">';
                foreach ($field['fields'] as $sub_field) {
                    self::render_field($sub_field);
                }
                echo '</div>';
                break;
				
			// 日期
			case 'date':
				echo '<input type="date" id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '">';
				break;
			// 下拉框
			case 'select':
				echo '<select id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '">';
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
                        self::render_field($sub_field);
                    }
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
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

            default:
                echo '<p>未知字段类型：' . esc_html($field['type']) . '</p>';
                break;
        }

        echo '</div>';
    }
}