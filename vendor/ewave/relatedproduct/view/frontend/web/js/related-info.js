define([
    'jquery',
    './dist/common/component',
    'jquery/ui'
], function ($, Component) {
    'use strict';

    $.widget('ewave.relatedInfo', {
        options: {
            filterable: true,
            initialFilter: '',
            filterItems: '.related-filters > .item',
            filterInfo: '.related-filters > .content',
            filterActiveState: '-active',
            gridId: 'related-grid',
            switchElement: '[data-role="related-details"]',
            expandable: true,
            expanderElement: '[data-expander]',
            expanderTemplate: '<div class="related-expander"><%= data.html %></div>',
            expanderHolder: '.related-expander',
            scroll: false,
            scrollTo: 'details'
        },

        _create: function () {
            new Component(this.options, this.element);
        }
    });
    
    return $.ewave.relatedInfo;
});
