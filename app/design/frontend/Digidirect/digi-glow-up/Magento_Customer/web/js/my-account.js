require(['jquery'], function ($) {
    $(window).on('load', function () {
        setTimeout(function () {
            if ($('.form-edit-account .customer-dob #dob').val()) {
                $('.form-edit-account .customer-dob .ui-datepicker-trigger').hide();
                $('.form-edit-account .field-dob').attr('style', 'width: 49% !important;');
            }
        }, 500);
    });
});
