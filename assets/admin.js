document.addEventListener("DOMContentLoaded", function() {
    // 切换菜单
    let menuItems = document.querySelectorAll(".fox-sidebar li");
    let sections = document.querySelectorAll(".fox-content .fox-section");

    menuItems.forEach((item, index) => {
        item.addEventListener("click", function() {
            menuItems.forEach(el => el.classList.remove("active"));
            sections.forEach(el => el.style.display = "none");

            item.classList.add("active");
            sections[index].style.display = "block";
        });
    });

    // 颜色选择器
    jQuery('.fox-color-picker').wpColorPicker();

    // 媒体上传
    jQuery('.fox-media-upload').click(function(e) {
        e.preventDefault();
        let button = jQuery(this);
        let inputField = button.siblings('.fox-media-url');

        let mediaUploader = wp.media({
            title: '选择或上传图片',
            button: { text: '使用此图片' },
            multiple: false
        }).on('select', function() {
            let attachment = mediaUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
            button.siblings('.fox-media-preview').attr('src', attachment.url).show();
            button.siblings('.fox-media-remove').show();
        }).open();
    });

    // 媒体移除
    jQuery('.fox-media-remove').click(function(e) {
        e.preventDefault();
        let button = jQuery(this);
        button.siblings('.fox-media-url').val('');
        button.siblings('.fox-media-preview').hide();
        button.hide();
    });

    // 图片集上传
    jQuery('.fox-gallery-wrapper').on('click', '.fox-gallery-upload', function(e) {
        e.preventDefault();
        let button = jQuery(this);
        let inputField = button.siblings('.fox-gallery-url');

        let mediaUploader = wp.media({
            title: '选择或上传图片',
            button: { text: '使用此图片' },
            multiple: false
        }).on('select', function() {
            let attachment = mediaUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
            inputField.siblings('.fox-gallery-preview').attr('src', attachment.url).show();
            // 更新隐藏输入框的值
            let wrapper = inputField.closest('.fox-gallery-wrapper');
            let urls = [];
            wrapper.find('.fox-gallery-url').each(function() {
                let url = jQuery(this).val();
                if (url) {
                    urls.push(url);
                }
            });
            wrapper.find('input[type="hidden"]').val(urls.join(','));
        }).open();
    });

    // 图片集添加项
    jQuery('.fox-gallery-wrapper').on('click', '.fox-gallery-add', function(e) {
        e.preventDefault();
        let wrapper = jQuery(this).closest('.fox-gallery-wrapper');
        let newItem = jQuery('<div class="fox-gallery-item">');
        newItem.append('<input type="text" class="fox-gallery-url" value="">');
        newItem.append('<button type="button" class="button fox-gallery-upload">上传图片</button>');
        newItem.append('<button type="button" class="button fox-gallery-add">+</button>');
        newItem.append('<button type="button" class="button fox-gallery-remove">-</button>');
        newItem.append('<img src="" class="fox-gallery-preview" style="max-width: 100px; margin: 5px; display: none;">');
        jQuery(this).remove();
        newItem.appendTo(wrapper.find('.fox-gallery-items'));
        // 更新隐藏输入框的值
        updateGalleryHiddenInput(wrapper);
    });

    // 图片集移除项
    jQuery('.fox-gallery-wrapper').on('click', '.fox-gallery-remove', function(e) {
        e.preventDefault();
        let wrapper = jQuery(this).closest('.fox-gallery-wrapper');
        if (wrapper.find('.fox-gallery-item').length > 1) {
            jQuery(this).closest('.fox-gallery-item').remove();
            // 更新隐藏输入框的值
            updateGalleryHiddenInput(wrapper);
        }
    });

    // 更新隐藏输入框的值
    function updateGalleryHiddenInput(wrapper) {
        let urls = [];
        wrapper.find('.fox-gallery-url').each(function() {
            let url = jQuery(this).val();
            if (url) {
                urls.push(url);
            }
        });
        wrapper.find('input[type="hidden"]').val(urls.join(','));
    }

    // 重复器添加项
    jQuery('.fox-repeater-add').click(function(e) {
        e.preventDefault();
        let repeater = jQuery(this).closest('.fox-repeater');
        let item = repeater.find('.fox-repeater-item:first').clone();
        item.find('input, select, textarea').val('');
        item.appendTo(repeater);
    });

    // 重复器移除项
    jQuery('.fox-repeater-remove').click(function(e) {
        e.preventDefault();
        jQuery(this).closest('.fox-repeater-item').remove();
    });

    // 手风琴效果
    jQuery('.fox-accordion-title').click(function() {
        jQuery(this).next('.fox-accordion-content').slideToggle();
    });

    // 处理依赖关系
    document.querySelectorAll('.fox-field input, .fox-field select, .fox-field textarea').forEach(function(input) {
        input.addEventListener('change', function() {
            document.querySelectorAll('.fox-field[data-dependency]').forEach(function(field) {
                var dependency = field.getAttribute('data-dependency').split(':');
                var dependencyField = dependency[0];
                var dependencyValue = dependency[1];
                var dependencyFieldInput = document.querySelector('#' + dependencyField);

                if (dependencyFieldInput) {
                    var currentValue;
                    if (dependencyFieldInput.type === 'checkbox') {
                        currentValue = dependencyFieldInput.checked ? '1' : '0';
                    } else {
                        currentValue = dependencyFieldInput.value;
                    }
                    if (currentValue === dependencyValue) {
                        field.style.display = 'block';
                    } else {
                        field.style.display = 'none';
                    }
                }
            });

            // 显示配置改变提示
            if (!document.getElementById('config-changed-warning')) {
                let warning = document.createElement('div');
                warning.id = 'config-changed-warning';
                warning.classList.add('warning');
                warning.textContent = '配置发生改变，请勿忘记保存';
                document.querySelector('.fox-header').appendChild(warning);
            }
        });
    });
	
jQuery(document).ready(function($) {
    // 监听所有复选框的状态变化
    $('input[type="checkbox"]').on('change', function() {
        var checkboxId = $(this).attr('id');
        var checkboxValue = $(this).is(':checked') ? '1' : '';

        // 查找所有依赖于该复选框的字段
        $('[data-dependency^="' + checkboxId + '"]').each(function() {
            var dependency = $(this).data('dependency');
            var parts = dependency.split(':');
            var dependencyField = parts[0];
            var dependencyValue = parts[1];

            if (checkboxId === dependencyField) {
                if (checkboxValue === dependencyValue) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            }
        });
    });
});	

    // 保存按钮点击事件
    jQuery('form').on('submit', function(e) {
        e.preventDefault();
        let form = jQuery(this);
        let data = form.serialize();
        let menuSlug = form.find('input[name="option_page"]').val();

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'fox_save_options',
                menu_slug: menuSlug,
                data: data
            },
            success: function(response) {
                if (response.success) {
                    // 移除配置改变提示
                    let warning = document.getElementById('config-changed-warning');
                    if (warning) {
                        warning.remove();
                    }
                    // 显示保存成功提示
                    if (!document.getElementById('save-success-message')) {
                        let message = document.createElement('div');
                        message.id = 'save-success-message';
                        message.classList.add('success');
                        message.textContent = '保存成功';
                        document.querySelector('.fox-header').appendChild(message);
                        setTimeout(function() {
                            message.remove();
                        }, 3000);
                    }
                } else {
                    // 显示保存失败提示
                    if (!document.getElementById('save-failure-message')) {
                        let message = document.createElement('div');
                        message.id = 'save-failure-message';
                        message.classList.add('error');
                        message.textContent = '保存失败，请重试';
                        document.querySelector('.fox-header').appendChild(message);
                        setTimeout(function() {
                            message.remove();
                        }, 3000);
                    }
                }
            },
            error: function() {
                // 显示保存失败提示
                if (!document.getElementById('save-failure-message')) {
                    let message = document.createElement('div');
                    message.id = 'save-failure-message';
                    message.classList.add('error');
                    message.textContent = '保存失败，请重试';
                    document.querySelector('.fox-header').appendChild(message);
                    setTimeout(function() {
                        message.remove();
                    }, 3000);
                }
            }
        });
    });
});