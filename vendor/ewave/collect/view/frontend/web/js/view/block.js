define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'Ewave_Collect/js/model/shipping-address/single-cart-form-popup-state',
    'Ewave_Collect/js/model/block',
    'Ewave_Collect/js/action/set-block-places',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage'
], function ($, _, ko, Component, $t, modal, formPopUpState, collectPlaces, setBlockPlaces, urlBuilder, storage) {
    'use strict';

    var singleCartPopUp = null,
        isCollectEnableOnCheckout = window.checkoutConfig.quoteData.is_collect_enable_on_checkout,
        isSingleCartCollectVariation = window.checkoutConfig.quoteData.is_single_cart_collect_variation,
        quoteCollectPlaces = window.checkoutConfig.quoteData.collect_places,
        placesUrl = window.checkoutConfig.quoteData.get_places_url,
        distanceList = window.checkoutConfig.quoteData.distance_list;

    return Component.extend({
        defaults: {
            collectBlock: '[data-role="collect-block"]',
            visibleClass: '-visible',
            collectFormTemplate: 'Ewave_Collect/checkout/shipping-address/block-form'
        },
        collectPlaces: collectPlaces.places,
        collectPlaceRows: ko.observableArray([]),
        initialize: function () {
            var self = this;

            setBlockPlaces();

            this._super();

            this.isSingleCartFormPopUpVisible.subscribe(function (value) {
                if (value) {
                    self.getPopUp().openModal();
                }
            });
        },
        formItemId: '',
        isSingleCartFormPopUpVisible: formPopUpState.isVisible,
        isCollectEnableOnCheckout: ko.observable(isCollectEnableOnCheckout || false),
        isSingleCartCollectVariation: ko.observable(isSingleCartCollectVariation || false),
        placesUrl: placesUrl,
        distanceList: distanceList,
        getPopUp: function () {
            var self = this;

            $('#collect_quote_item_id').val(this.formItemId);

            if (!singleCartPopUp) {
                this.popUpForm.options.buttons = [];
                this.popUpForm.options.closed = function () {
                    self.isSingleCartFormPopUpVisible(false);
                };

                this.popUpForm.options.modalCloseBtnHandler = this.onClosePopUp.bind(this);
                this.popUpForm.options.keyEventHandlers = {
                    escapeKey: this.onClosePopUp.bind(this)
                };

                singleCartPopUp = modal(this.popUpForm.options, $(this.popUpForm.element));
            }

            return singleCartPopUp;
        },
        onClosePopUp: function () {
            this.getPopUp().closeModal();
        },
        showFormPopUp: function (data, e) {
            if (this.collectPlaces().length > 0) {
                this.formItemId = this.collectPlaces()[0].item_id;
            }
            this.isSingleCartFormPopUpVisible(true);
        },
        getDeliveryPlace: function (item) {
            if (item) {
                if (quoteCollectPlaces && quoteCollectPlaces[item.item_id]) {
                    return true;
                }
            }
            return false;
        },
        submitCollect: function () {
            var collectPlaceId = $('input[name=collect_place_id]:checked', this.popUpForm.element).val(),
                collectPlaceStorageName = $('input[name=collect_place_id]:checked', this.popUpForm.element).attr('data-collectplace-storage-name');

            this.applyCollectPlaceToAllItems(collectPlaceId, collectPlaceStorageName);
        },
        searchCollectPlaces: function (element) {
            var self = this,
                collectQty = 1,
                $form = $(element).closest('form');

            if ($form.length) {
                if (!$form.valid()) {
                    return;
                }
            }

            _.each(window.checkoutConfig.quoteItemData, function (item) {
                if (self.formItemId === item.item_id) {
                    collectQty = item.qty;
                    return true;
                }
            });

            $.ajax({
                url: placesUrl,
                data: {
                    collect_postcode: $('#collect_postcode').val(),
                    collect_distance: $('#collect_distance').val(),
                    collect_qty: collectQty,
                    quote_item_id: $('#collect_quote_item_id').val()
                },
                success: function (result) {
                    self.collectPlaceRows.removeAll();
                    _.each(result.data, function (item) {
                        self.collectPlaceRows.push(item);
                    });
                }
            });
        },
        updateCollectPlaces: function (data) {
            this.collectPlaces.unshift(data[0]);
        },
        updateStorageName: function (id) {
            $('#collect_place_storage_name').val(id);

            return true;
        },
        toggleCollectType: function (data, e) {
            var method = $(e.currentTarget).val();
            $(this.collectBlock).removeClass(this.visibleClass);
            $('[data-collect-type="' + method + '"]').addClass(this.visibleClass);

            if (method === 'delivery') {
                this.applyDeliveryToAllItems();
            }
        },
        applyDeliveryToAllItems: function () {
            var self = this,
                serviceUrl = urlBuilder.createUrl('/collectplace/delivery/all', {});

            storage.post(serviceUrl).done(function (response) {
                self.onSuccessDelivery(response);
            }).fail(function (response) {
                self.onErrorDelivery(response);
            });
        },
        onSuccessDelivery: function (response) {
            this.collectPlaces.removeAll();
        },
        onErrorDelivery: function (response) {},
        applyCollectPlaceToAllItems: function (id, name) {
            var self = this,
                serviceUrl = urlBuilder.createUrl('/collectplace/apply/all', {}),
                payload = {
                    collectPlaceId: id,
                    storageName: name
                };

            storage.post(serviceUrl, JSON.stringify(payload)).done(function (response) {
                self.onSuccessApplyPlace(response);
            }).fail(function (response) {
                self.onErrorApplyPlace(response);
            });
        },
        onSuccessApplyPlace: function (response) {
            this.onClosePopUp();
            this.updateCollectPlaces(JSON.parse(response));
            this.collectPlaceRows.removeAll();
        },
        onErrorApplyPlace: function (response) {}
    });
});
