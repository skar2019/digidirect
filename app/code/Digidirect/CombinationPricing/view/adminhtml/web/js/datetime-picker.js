require([
    'jquery',
    'mage/calendar'
], function($) {
    'use strict';
    
    function initDateTimePickers() {
        $('.datetime-picker').each(function() {
            if (!$(this).hasClass('hasDatepicker')) {
                $(this).calendar({
                    dateFormat: 'dd/mm/yy',  // Magento format: day/month/year
                    showsTime: true,
                    timeFormat: 'HH:mm:ss',
                    buttonText: 'Select Date & Time',
                    showOn: 'button',
                    buttonImage: null,
                    sideBySide: false
                });
            }
        });
    }
    
    // Initialize on page load
    $(document).ready(function() {
        initDateTimePickers();
        
        // Re-initialize when Add button is clicked
        $(document).on('click', 'button.action-add', function() {
            setTimeout(initDateTimePickers, 500);
        });
    });
});