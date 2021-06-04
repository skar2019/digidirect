define([
    'underscore',
    'ko',
    'Ewave_Collect/js/model/block'
], function (_, ko, collect) {
    'use strict';

    var quoteCollectPlaces = window.checkoutConfig.quoteData.collect_places;

    return function () {
        _.each(quoteCollectPlaces, function (place) {
            collect.places.push(place);
        });
    };
});
