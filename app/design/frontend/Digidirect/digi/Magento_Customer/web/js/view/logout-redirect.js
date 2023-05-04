/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/mage'
], function ($) {
    'use strict';

    return function (data) {
        console.log("Override Logout Redirect!");
        $($.mage.redirect(data.url, 'assign', 5000));
    };
});
