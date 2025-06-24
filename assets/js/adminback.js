document.addEventListener("DOMContentLoaded", function() {
    // **切换菜单**
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

    // **颜色选择器**
    jQuery('.fox-color-picker').wpColorPicker();

    // **媒体上传**
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
        }).open();
    });
});
