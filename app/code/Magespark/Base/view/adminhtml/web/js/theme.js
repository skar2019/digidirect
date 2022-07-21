define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.globalNavigation', widget, {
            _close: function (e) {

                var selectors = this.options.selectors,
                    menuItem = $(e.target).closest(selectors.topLevelItem),
                    subMenu = $(selectors.subMenu, menuItem),
                    closeBtn = subMenu.find(selectors.closeSubmenuBtn),
                    blur = this._blur.bind(this);

                e.preventDefault();

                this.overlay.hide(0).off('click');

                this.menuLinks.last().on('blur', blur);

                closeBtn.off('click');

                subMenu.attr('aria-expanded', 'false');

                menuItem.removeClass('_show _active');
            }
        });

        return $.mage.globalNavigation;
    }
});