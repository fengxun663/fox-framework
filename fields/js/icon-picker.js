jQuery(document).ready(function($) {
    $('.fox-icon-picker-button').on('click', function() {
        $(this).siblings('.fox-icon-picker-container').toggle();
    });

    $('.fox-icon-picker-container .fox-icon').on('click', function() {
        var iconClass = $(this).data('icon');
        $(this).closest('.fox-field').find('input[type="text"]').val(iconClass);
        $(this).closest('.fox-icon-picker-container').hide();
    });
});