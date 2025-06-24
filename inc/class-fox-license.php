<?php
if (!defined('ABSPATH')) {
    exit; // 禁止直接访问
}

class Fox_License {
    private static $instance = null;
    private $api_url = 'http://sq.xiongxiaofeng.com/sq/api/verify-license.php'; // 服务器 API 地址
    private $license_option = 'fox_framework_license';
    private $license_status_option = 'fox_framework_license_status';
    private $license_key_file = 'fox-license.php';

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 只有激活码有效时，才显示授权菜单
        if ($this->is_license_valid()) {
            add_action('admin_menu', [$this, 'add_license_menu']);
        }
    }

    /**
     * 添加授权管理菜单
     */
    public function add_license_menu() {
        add_menu_page('授权管理', '授权管理', 'manage_options', 'fox-license', [$this, 'license_page']);
    }

    /**
     * 授权管理页面
     */
    public function license_page() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fox_license_key'])) {
            $this->activate_license($_POST['fox_license_key']);
        }

        $status = get_option($this->license_status_option, '未激活');
        ?>
        <div class="wrap">
            <h1>灵狐框架授权</h1>
            <p>当前授权状态：<strong><?php echo esc_html($status); ?></strong></p>
            <p>激活时间：<strong><?php echo esc_html($activation_time); ?></strong></p>
            <p>有效期至：<strong><?php echo esc_html($expiration_time); ?></strong></p>
            <form method="post">
                <label for="fox_license_key">输入您的激活码：</label>
                <input type="text" name="fox_license_key" required>
                <button type="submit">激活</button>
            </form>
        </div>
        <?php
    }

    /**
     * 读取 `fox-license.php` 中的激活码
     */
    private function get_license_key_from_file() {
        $file_path = get_template_directory() . '/' . $this->license_key_file;
        if (file_exists($file_path)) {
            include $file_path;
            return defined('FOX_FRAMEWORK_LICENSE_KEY') ? FOX_FRAMEWORK_LICENSE_KEY : '';
        }
        return '';
    }

    /**
     * 激活 License
     */
    public function activate_license($license_key) {
        $stored_license_key = $this->get_license_key_from_file();
        if ($license_key !== $stored_license_key) {
            update_option($this->license_status_option, '激活码不匹配');
            return;
        }

        $response = $this->verify_license_from_server($license_key);
        if ($response && $response['status'] == 'valid') {
            update_option($this->license_option, $license_key);
            update_option($this->license_status_option, '已激活');
        } else {
            update_option($this->license_status_option, '激活失败');
        }
    }

    /**
     * 从服务器验证激活码
     */
    public function verify_license_from_server($license_key) {
        $response = wp_remote_post($this->api_url, [
            'method' => 'POST',
            'body'   => [
                'license_key' => $license_key,
                'domain'      => $_SERVER['SERVER_NAME'],
                'ip'          => $_SERVER['SERVER_ADDR'],
            ],
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    /**
     * 判断 License 是否有效
     */
    public function is_license_valid() {
        return get_option($this->license_status_option) === '已激活';
    }

    /**
     * 静态方法供 `fox-framework.php` 调用
     */
    public static function is_activated() {
        return get_option('fox_framework_license_status') === '已激活';
    }
}

Fox_License::get_instance();
