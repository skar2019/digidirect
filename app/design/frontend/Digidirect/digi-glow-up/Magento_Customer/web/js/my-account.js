require(['jquery',
    'Magento_Customer/js/flatpickr.min',
    'mage/calendar'
    ], function ($, flatpickr) {
        $(document).ready(function () {
            $('.account input[name="order-date-from"], .account input[name="order-date-to"], .account input[name="dob"], .account input[name="digiclub-dob"]').removeClass('_has-datepicker');
            $('.ui-datepicker-trigger').hide();

            $('input[name="order-date-from"]').after('<button class="ui-datepicker-trigger-glowup icon-from"></button>');
            $('input[name="order-date-to"]').after('<button class="ui-datepicker-trigger-glowup icon-to"></button>');

            const selectors = [
                { input: 'input[name="order-date-from"]', icon: '.icon-from' },
                { input: 'input[name="order-date-to"]', icon: '.icon-to' },
                { input: 'input[name="dob"]', icon: '.icon-dob' },
                { input: 'input[name="digiclub-dob"]', icon: '.icon-dob-digiclub' }
            ];

            if ($('.form-edit-account .customer-dob #dob').val()) {
                $('.form-edit-account .field-dob').attr('style', 'width: 49% !important;');
                $('.form-edit-account .customer-dob').attr('style', 'opacity: 0.6 !important;');
                $('.form-edit-account .customer-dob #dob').attr('style', 'cursor: default !important;');
            } else {
                $('input[name="dob"]').after('<button class="ui-datepicker-trigger-glowup icon-dob"></button>');
            }

            if (!$('input[name="digiclub-dob"]').val()) {
                $('input[name="digiclub-dob"]').after('<button class="ui-datepicker-trigger-glowup icon-dob-digiclub"></button>');
            } else {
                $('input[name="digiclub-dob"]').attr('style', 'opacity: 0.6 !important;');
                $('input[name="digiclub-dob"]').attr('style', 'cursor: default !important;');
            }

            selectors.forEach(({ input, icon }) => {
                const instance = flatpickr(input, {
                    dateFormat: 'd/m/Y',
                    //allowInput: true,
                    clickOpens: true,
                    maxDate: 'today',
                    parseDate: (datestr) => {
                        return new Date(datestr.replace(/-/g, '/'));
                    }
                });

                $(icon).on('click', function (e) {
                    e.preventDefault();
                    instance.open();
                });
            });

        });
});
