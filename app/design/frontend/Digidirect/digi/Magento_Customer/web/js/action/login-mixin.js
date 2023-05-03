/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/storage',
    'Magento_Ui/js/model/messageList',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function ($, storage, globalMessageList, customerData, $t) {
    'use strict';

    var callbacks = [],

        /**
         * @param {Object} loginData
         * @param {String} redirectUrl
         * @param {*} isGlobal
         * @param {Object} messageContainer
         */
        action = function (loginData, redirectUrl, isGlobal, messageContainer) {
            messageContainer = messageContainer || globalMessageList;

            return storage.post(
                'customer/ajax/login',
                JSON.stringify(loginData),
                isGlobal
            ).done(function (response) {
                console.log("Override Login!");
                if (response.errors) {
                    messageContainer.addErrorMessage(response);
                    callbacks.forEach(function (callback) {
                        callback(loginData);
                    });
                } else {
                    callbacks.forEach(function (callback) {
                        callback(loginData);
                    });
                    customerData.invalidate(['customer']);

                    if (response.redirectUrl) {
                        window.location.href = response.redirectUrl;
                    } else if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else {
                        location.reload();
                    }
                    
                    const loadconfigdata = localStorage.getItem("configData-60007039-e927-ec11-aaf7-061f6a8be99c");
                    const myObj = JSON.parse(loadconfigdata);
                    var magento_pa_id = myObj.c;

                    localStorage.setItem("Magento_PA_Id", magento_pa_id); 
                    alert("magento_pa_id: " + magento_pa_id);

                    var param = '{pa_id=' + magento_pa_id + '}';
                    var YOUR_URL_HERE = 'getpaidsalesforce';

                    jQuery.ajax({
                        url: YOUR_URL_HERE,
                        type: "POST",
                        data: {pa_id: param},
                        dataType: "json",
                        success: function() {
                            alert("Thank you for subscribing!");
                        },
                        error: function() {
                            alert("There was an error. Try again please!");
                        }
                    });
                }
            }).fail(function () {
                messageContainer.addErrorMessage({
                    'message': $t('Could not authenticate. Please try again later')
                });
                callbacks.forEach(function (callback) {
                    callback(loginData);
                });
            });
        };

    /**
     * @param {Function} callback
     */
    action.registerLoginCallback = function (callback) {
        callbacks.push(callback);
    };

    return action;
});
