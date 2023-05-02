define([
    'ko',
    'uiComponent',
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'underscore',
    'mage/cookies'
], function (ko, Component, $, modal, _) {
    'use strict';

    return Component.extend({
        templateToRender: 'Digidirect_PaSalesForceProductRecommendation/template/login_popup.html',
        renderer: null,
        data: {"name": "Digidirect", "module": "PaSalesForceProductRecommendation"},
        initialize: function (config) {
            this._super();
            let options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Login Popup',
                buttons: [{
                    text: $.mage.__('OK'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };
            require(['text!' + this.templateToRender], function (templateContents) {
                this.renderer = _.template(templateContents);
                let self = this;
                let data = self.data;
                if(self.showPopup()) {
                    modal({
                        title: $.mage.__('Login Popup'),
                        content: self.render({data}),
                        modalClass: 'confirm login-confirm',
                        actions: {
                            cancel: function(){
                                $.mage.cookies.set('can_show_login_popup', '0', {
                                    samesite: 'strict',
                                    domain: ''
                                });
                                if($.mage.cookies.get('show_popup_on_each_config') === "0") {
                                    $.mage.cookies.set('displayed_login_popup', '1', {
                                        samesite: 'strict',
                                        domain: ''
                                    });
                                }
                            },
                            always: function(){}
                        },
                        buttons: [{
                            text: $.mage.__('Cancel'),
                            class: 'action-secondary action-dismiss',
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }]
                    });
                }
            }.bind(this));
        },
        render: function (data) {
            return this.renderer(data);
        }
    });

});