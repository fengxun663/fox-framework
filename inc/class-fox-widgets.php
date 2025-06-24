<?php
if (!defined('ABSPATH')) {
    exit;
}

class Fox_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'fox_widget',
            '自定义小工具',
            array('description' => '这是一个自定义小工具')
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        echo '<p>' . esc_html($instance['text']) . '</p>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title =!empty($instance['title']) ? $instance['title'] : '';
        $text =!empty($instance['text']) ? $instance['text'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('text'); ?>">内容:</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" rows="3"><?php echo esc_textarea($text); ?></textarea>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['text'] = (!empty($new_instance['text'])) ? sanitize_textarea_field($new_instance['text']) : '';
        return $instance;
    }
}

function fox_register_widgets() {
    register_widget('Fox_Widget');
}
add_action('widgets_init', 'fox_register_widgets');