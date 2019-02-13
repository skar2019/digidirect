/* eslint no-useless-escape: [0] */
define([
    'jquery',
    'underscore',
    'Ewave_MyStoreWidget/js/dist/common/component',
    'jquery/ui',
    'mage/translate',
    'mage/validation'
], function ($, _, Component) {
    'use strict';

    $.widget('ewave.myStoreSwitcher', {
        options: {
            container: '.block-mystorebar',
            form: '#mystore-form',
            input: '#mystore-input',
            findStoreInput: '#mystore_find_store',
            entityId: '#abstract_entity_id',
            openAction: '[data-action="set-store"]',
            focusAction: '[data-action="focus-store"]',
            changeAction: '[data-action="change-store"]',
            saveAction: '#block-mystorebar [data-action="save-mystore"]',
            hideSelector: '-hide',
            selectedSelector: '-selected',
            noResultsSelector: '-no-results',
            saveUrl: '',
            isAjaxSave: false,
            searchUrl: '',
            searchNoResults: $.mage.__('No matches found.'),
            searchResultFormat: '%name (%state, %postcode)',
            isAutocompleteEnabled: true,
            isGoogleAutoSuggestEnabled: false,
            googleAutoSuggestApiKey: null,
            googleInputPrefix: '#mystore_',
            singleCountryData: null,
            autocompleteSettings: {
                appendTo: '#mystore-control',
                minLength: 0
            },
            storesList: [],
            isGeoLocationEnabled: false,
            isNeedKeepGeoLocation: false,
            isSelectedStore: false,
            noticeTemplate: '<span class="notice">' + $.mage.__('Sorry, you need allow your browser use Geo location to find a store.') + '</span>'
        },
        _create: function () {
            this._bind();

            if (this.options.isGoogleAutoSuggestEnabled) {
                this._initGoogleAutocomplete();
            } else {
                this._initAutocomplete();
                this._initGeoLocation();
            }
        },
        _bind: function () {
            var self = this;

            $(document).on('click', this.options.openAction, function (e) {
                e.preventDefault();
                self.open();
            });

            $(document).on('click', this.options.focusAction, function (e) {
                e.preventDefault();
                self.focus();
            });

            $(document).on('click', this.options.changeAction, function (e) {
                e.preventDefault();
                self.change($(this));
            });

            if (this.options.isAjaxSave) {
                $(document).on('click', this.options.saveAction, function (e) {
                    e.preventDefault();
                    if ($(self.options.form).valid()) {
                        self.save();
                    }
                });
            }
        },
        /**
         * Initialize Google Places Autocomplete
         * @private
         */
        _initGoogleAutocomplete: function () {
            new Component(this.options);
        },
        /**
         * Initialize autocomplete widget
         * @private
         */
        _initAutocomplete: function () {
            if (this.options.isAutocompleteEnabled) {
                var options = $.extend({}, this.autocompleteOptions(), this.options.autocompleteSettings);
                $(this.options.input).autocomplete(options);
            }
        },
        /**
         * Autocomplete options
         * @returns {{source: source, select: select}}
         */
        autocompleteOptions: function () {
            var self = this;
            return {
                create: function () {
                    $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                        return self.getAutocompeleteItemFormat(ul, item);
                    };
                },
                source: function (request, response) {
                    self.sendRequest(request, response);
                },
                select: function (event, ui) {
                    self.onSelect(event, ui);
                }
            };
        },
        /**
         * Send search request
         * @param request
         * @param response
         */
        sendRequest: function (request, response) {
            var self = this;
            $.ajax({
                url: self.options.searchUrl,
                data: {'store_name': request.term},
                dataType: 'json',
                success: function (data) {
                    self.onSuccessRequest(request, response, data);
                },
                error: function (xhr, status) {
                    self.onErrorRequest(xhr, status);
                }
            });
        },
        /**
         * Success request
         * @param request
         * @param response
         * @param data
         */
        onSuccessRequest: function (request, response, data) {
            var self = this;
            if (data.items !== undefined) {
                if (_.isEmpty(data.items)) {
                    response(self.formatEmptyResponse(request));
                } else {
                    response($.map(data.items, function (item) {
                        return self.formatResponse(item);
                    }));
                }
            }
        },
        /**
         * Error of request
         * @param xhr
         * @param status
         */
        onErrorRequest: function (xhr, status) {
        },
        /**
         * Select autocomplete item
         * @param event
         * @param ui
         */
        onSelect: function (event, ui) {
            $(this.options.entityId).val(ui.item.entityId);
        },
        /**
         * Format of response
         * @param item
         */
        formatResponse: function (item) {
            if (item.name) {
                var label = this.formatLabel(item);
                $(this.options.container).removeClass(this.options.noResultsSelector);
                return {
                    label: label,
                    value: this.clearLabelValue(label),
                    entityId: item.entity_id
                };
            }
        },
        formatLabel: function (item) {
            var format = this.options.searchResultFormat,
                attributesArray,
                result = item.name;
            if (format) {
                attributesArray = format.match(/[a-z1-9\_\-]+/ig);
                if (attributesArray.length) {
                    $.each(attributesArray, function (key, value) {
                        var val = item[value];
                        if (val !== undefined) {
                            format = format.replace('%' + value, val);
                        }
                    });
                    format = this.formatLabelPostProcess(format);
                    return format;
                }
            }
            result = this.formatLabelPostProcess(result);
            return result;
        },
        formatLabelPostProcess: function (str) {
            str = str.replace(/\S+\%\S+/ig, '');
            str = $.trim(str.replace(/\%\S+/ig, ''));
            return str;
        },
        clearLabelValue: function (label) {
            return $('<div />').html(label).text();
        },
        /**
         * Format of empty response
         * @returns {*}
         */
        formatEmptyResponse: function (request) {
            $(this.options.container).addClass(this.options.noResultsSelector);
            if (this.options.searchNoResults) {
                return [{
                    label: this.options.searchNoResults,
                    value: request.term
                }];
            }
            return [];
        },
        getAutocompeleteItemFormat: function (ul, item) {
            return $('<li class="item">').append($('<a class="link">').html(item.label)).appendTo(ul);
        },
        /**
         * Initialize HTML5 GeoLocation
         * @private
         */
        _initGeoLocation: function () {
            if (this.options.isGeoLocationEnabled) {
                var self = this;
                if (navigator.geolocation) {
                    if (this.options.isNeedKeepGeoLocation && this.isLocationCached()) {
                        this.showGeoLocationResult(JSON.parse(window.localStorage.getItem('myStoreLocation')));
                    } else {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            self.getUserLocation(position);
                        }, function (error) {
                            self.showGeoLocationError(error);
                        });
                    }
                } else {
                    console.warn('The browser does not support Geolocation.');
                }
            }
        },
        /**
         * Callback function for asynchronous call to HTML5 GeoLocation
         * @param position
         */
        getUserLocation: function (position) {
            this.getNearestLocation(position.coords.latitude, position.coords.longitude);
        },
        /**
         * Get Nearest Location
         * @param latitude
         * @param longitude
         */
        getNearestLocation: function (latitude, longitude) {
            var self = this,
                minDiff = 99999,
                closest,
                diff,
                cities = this.options.storesList;

            if (cities.length) {
                _.each(cities, function (value, key) {
                    if (value.latitude && value.longitude) {
                        diff = self.pythagorasEquirectangular(latitude, longitude, +value.latitude, +value.longitude);
                        if (diff < minDiff) {
                            closest = key;
                            minDiff = diff;
                        }
                    }
                });

                if (closest !== undefined) {
                    this.showGeoLocationResult(cities[closest]);
                    if (this.options.isNeedKeepGeoLocation) {
                        this.saveGeoLocation(cities[closest]);
                    }
                }
            }
        },
        /**
         * Convert Degrees to Radians
         * @param deg
         * @returns {number}
         */
        deg2Rad: function Deg2Rad (deg) {
            return deg * Math.PI / 180;
        },
        /**
         * Pythagoras formula
         * R - Radius of the earth in km
         * @param lat1
         * @param lon1
         * @param lat2
         * @param lon2
         * @returns {number}
         */
        pythagorasEquirectangular: function (lat1, lon1, lat2, lon2) {
            lat1 = this.deg2Rad(lat1);
            lat2 = this.deg2Rad(lat2);
            lon1 = this.deg2Rad(lon1);
            lon2 = this.deg2Rad(lon2);
            var R = 6371,
                x = (lon2 - lon1) * Math.cos((lat1 + lat2) / 2),
                y = (lat2 - lat1);
            return Math.sqrt(x * x + y * y) * R;
        },
        /**
         * Show GeoLocation Result
         * @param data
         */
        showGeoLocationResult: function (data) {
            if (!this.options.isSelectedStore) {
                $(this.options.input).val(this.clearLabelValue(this.formatLabel(data)));
                $(this.options.entityId).val(data.entity_id);
            }
        },
        /**
         * Save location in localStorage
         * @param data
         */
        saveGeoLocation: function (data) {
            window.localStorage.setItem('myStoreLocation', JSON.stringify(data));
        },
        /**
         * Show GeoLocation Error
         * @param error
         */
        showGeoLocationError: function (error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    if (!this.options.isSelectedStore && this.options.noticeTemplate) {
                        $(this.options.container).append($(this.options.noticeTemplate));
                    }
                    break;
                default:
                    console.warn(error);
            }
        },
        /**
         * Determine whether the location is cached
         */
        isLocationCached: function () {
            return window.localStorage.getItem('myStoreLocation');
        },
        /**
         * Open store finder container
         */
        open: function () {
            $(this.options.container).removeClass(this.options.hideSelector);
        },
        /**
         * Close store finder container
         */
        close: function () {
            $(this.options.container).addClass(this.options.hideSelector);
        },
        /**
         * Change selected store
         * @param $element
         */
        change: function ($element) {
            var container = $element.data('container'),
                $container = $(container);
            if (!container) {
                $container = $element.closest(this.options.container);
            }
            $container.find(this.options.entityId).val('');
            $container.removeClass(this.options.selectedSelector);
            $container.find(this.options.input).focus();
        },
        /**
         * Focus on select bar field
         */
        focus: function () {
            this.open();
            $(this.options.input).focus();
        },
        /**
         * Dynamic save store
         */
        save: function () {
            var self = this,
                $form = $(this.options.form);

            $.ajax({
                url: this.options.saveUrl,
                method: 'POST',
                data: $form.serialize() + '&isAjax=true',
                dataType: 'json',
                beforeSend: function () {
                    $(document).trigger('mystore.save.send');
                },
                success: function (data) {
                    self.onSuccessSave(data);
                },
                error: function (xhr, status) {
                    self.onErrorSave(xhr, status);
                }
            });
        },
        onSuccessSave: function (data) {
            var self = this;

            if (!data.error) {
                $(this.options.container).addClass(this.options.selectedSelector);
                data.store.formatLabel = self.formatLabel(data.store);
            }
            $(document).trigger('mystore.save.success', [data]);
        },
        onErrorSave: function (xhr, status) {
            $(document).trigger('mystore.save.error', [xhr, status]);
        }
    });

    return $.ewave.myStoreSwitcher;
});
