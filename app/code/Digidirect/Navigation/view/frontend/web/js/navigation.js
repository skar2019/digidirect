define([
    'jquery',
    'Digidirect_Navigation/js/dist/common/component'
], function ($, Component) {
    'use strict';

    $.widget('digidirect.navigation', {
        options: {
            menu: [],
            area: '.navigation-wrapper',
            wrapperClass: '.menu-wrapper',
            itemClass: '.item',
            itemLabelClass: '.link',
            innerListsClass: '.menu',
            subMenuBlockClass: '.sub-menu',
            cmsBlockClassName: '.cms',
            expanded: false,
            place: 'main',
            horizontal: true,
            static: false,
            action: 'hover',
            responsive: true,
            togglerSelector: '[data-action="toggle-nav"]',
            offCanvasClass: 'offcanvas-open',
            offCanvasSide: 'left',
            offCanvasEvent: 'click',
            breakpoint: '768px',
            viewCustom: '',
            linkString: 'All {original}',
            addLinkToTop: false
        },

        _create: function () {
            new Component(this.options);
        }
    });

    return $.digidirect.navigation;
});
