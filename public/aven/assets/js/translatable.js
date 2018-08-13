$(document).on('change', '.js-tab-select', function () {
    $('.js-tab').removeClass('active').addClass('hidden');
    $('.js-tab-' + $(this).val()).addClass('active').removeClass('hidden');
    $('.js-tab-select').val($(this).val());
});