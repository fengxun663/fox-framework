# Fox Framework - 灵狐框架

一个强大、可扩展的 WordPress 主题选项框架。

## 功能特性

- 🎨 多种字段类型支持
- 📱 响应式后台界面
- 🔧 易于扩展和自定义
- 🌐 支持国际化
- 💾 自动保存和恢复

## 支持的字段类型

### 基础输入字段
- `text` - 单行文本输入
- `textarea` - 多行文本输入
- `number` - 数字输入
- `color` - 颜色选择器
- `image` - 图片上传

### 选择类字段
- `select` - 下拉选择
- `radio` - 单选按钮
- `checkbox` - 单个复选框
- `checkbox_group` - 多选复选框组
- `switch` - 开关按钮

### 高级字段
- `slider` - 滑块控制
- `code_editor` - 代码编辑器
- `group` - 字段分组

### 内容展示字段（新增）
- `content` - 显示静态内容/说明文本
- `heading` - 显示标题/分隔符
- `submessage` - 显示提示信息（支持多种样式）

## 使用方法

### 基本示例

```php
add_action('init', function () {
    fox_framework()->add_options([
        [
            'id'      => 'site_title',
            'title'   => '网站标题',
            'type'    => 'text',
            'default' => '',
        ],
    ]);
});
```

### Content 字段示例

Content 字段用于显示静态内容、说明文字等，不会保存到数据库：

```php
[
    'id'      => 'info_notice',
    'type'    => 'content',
    'content' => '<p>这里可以放置说明文字、帮助信息或者 HTML 内容。</p>',
]
```

### Heading 字段示例

Heading 字段用于显示标题和分隔不同的设置区域：

```php
[
    'id'      => 'heading_general',
    'type'    => 'heading',
    'content' => '常规设置',
]
```

### Submessage 字段示例

Submessage 字段用于显示提示信息，支持 5 种样式：

```php
// 普通样式
[
    'id'      => 'msg_info',
    'type'    => 'submessage',
    'style'   => 'normal',
    'content' => '这是一条普通提示信息。',
]

// 信息样式
[
    'id'      => 'msg_info',
    'type'    => 'submessage',
    'style'   => 'info',
    'content' => '这是一条信息提示。',
]

// 成功样式
[
    'id'      => 'msg_success',
    'type'    => 'submessage',
    'style'   => 'success',
    'content' => '操作成功！',
]

// 警告样式
[
    'id'      => 'msg_warning',
    'type'    => 'submessage',
    'style'   => 'warning',
    'content' => '<strong>警告：</strong>请谨慎操作！',
]

// 错误样式
[
    'id'      => 'msg_error',
    'type'    => 'submessage',
    'style'   => 'error',
    'content' => '<strong>错误：</strong>配置有误，请检查。',
]
```

### 完整示例

```php
add_action('init', function () {
    fox_framework()->add_options([
        // 添加标题分隔
        [
            'id'      => 'heading_basic',
            'type'    => 'heading',
            'content' => '基本设置',
        ],
        
        // 添加说明内容
        [
            'id'      => 'info_basic',
            'type'    => 'content',
            'content' => '<p>这里是基本设置区域，可以配置网站的基础信息。</p>',
        ],
        
        // 添加警告提示
        [
            'id'      => 'warning_basic',
            'type'    => 'submessage',
            'style'   => 'info',
            'content' => '所有更改都会立即生效，请确保正确填写。',
        ],
        
        // 添加实际的设置字段
        [
            'id'      => 'site_logo',
            'title'   => '网站 Logo',
            'type'    => 'image',
            'default' => '',
        ],
        
        [
            'id'      => 'site_title',
            'title'   => '网站标题',
            'type'    => 'text',
            'default' => '',
        ],
    ]);
});
```

## 获取选项值

在主题中获取保存的选项值：

```php
// 获取选项值
$logo = Fox_Options::get('site_logo', '默认值');

// 设置选项值
Fox_Options::set('site_logo', 'https://example.com/logo.png');
```

## 字段参数说明

### 通用参数

- `id` (必需) - 字段唯一标识符
- `title` (可选) - 字段标题（content/heading/submessage 不需要）
- `type` (必需) - 字段类型
- `default` (可选) - 默认值

### Content 字段特有参数

- `content` (必需) - 要显示的内容（支持 HTML）

### Heading 字段特有参数

- `content` (必需) - 标题文本

### Submessage 字段特有参数

- `content` (必需) - 提示内容（支持 HTML）
- `style` (可选) - 样式类型：`normal`、`info`、`success`、`warning`、`error`

## 版本信息

- 版本: 1.0.0
- 作者: Fox Framework Team
- 许可: GPL-2.0+

## 更新日志

### 1.0.0
- ✨ 新增 content 字段类型
- ✨ 新增 heading 字段类型
- ✨ 新增 submessage 字段类型（支持 5 种样式）
- 🐛 修复重复代码问题
- 🎨 优化 CSS 样式
- 📝 完善文档说明

