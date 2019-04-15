define([
    'jquery'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('ewave.locator', target, {
            initAutoComplete: function (field) {
                if ($(field).length) {
                    this.autocomplete = new google.maps.places.Autocomplete((document.querySelector(field)), {types: ['geocode']});
                    this.autocomplete.addListener('place_changed', this.searchAutoComplete.bind(this));
                }
            },

            searchAutoComplete: function () {
                this._search($(this.options.search.form))
            },

            extendRadius: function (markers) {
                var $radius = $(this.options.search.radius),
                    $nextRadius;
                if (this.options.radius.extensible && !markers.length && $radius.length) {
                    $nextRadius = $radius.find('option:selected').next();
                    if ($nextRadius.length) {
                        $nextRadius.attr('selected', 'selected');
                        if ($radius.data('selectric')) {
                            $radius.selectric('refresh');
                        }
                        this._search($(this.options.search.form));
                    }
                }
            }
        });

        return $.ewave.locator;
    };
});
