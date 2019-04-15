define([
    'ko',
    'uiComponent'
], function (ko, Component) {
    'use strict';
    var checkoutConfig = window.checkoutConfig,
        newslettersConfig = checkoutConfig ? checkoutConfig.checkoutNewsletterSubscribe : {};

    return Component.extend({
        defaults: {
            template: 'Ewave_Newsletter/checkout/checkout-newsletters'
        },
        isVisible: newslettersConfig.isEnabled,
        newsletters: newslettersConfig.newsletters
    });
});
