define([
    'jquery',
    'Magento_Theme/js/model/breadcrumb-list'
], function ($, breadcrumbList) {
    'use strict';

    return function (widget) {

        $.widget('mage.breadcrumbs', widget, {

            /**
             * Returns category menu item.
             *
             * Tries to resolve category from url or from referrer as fallback and
             * find menu item from navigation menu by category url.
             *
             * @return {Object|null}
             * @private
             */
            _resolveCategoryMenuItem: function () {
                var categoryUrl = this._resolveCategoryUrl(),
                    menu = $(this.options.menuContainer),
                    categoryMenuItem = null,
                    cmsLinkItem = null;

                // Check if category url equal base url
                if (categoryUrl+'/' === BASE_URL) {
                    return true;
                }

                if (categoryUrl && menu.length) {
                    categoryMenuItem = menu.find(
                        this.options.categoryItemSelector +
                        '> a[href="' + categoryUrl + '"]'
                    );
                }

                if (menu.find('.cms' + this.options.categoryItemSelector).length && !categoryMenuItem.length) {
                    var submenu = menu.find('.cms' + this.options.categoryItemSelector);

                    cmsLinkItem = submenu.find('a[href^="' + categoryUrl + '"]').first();
                    cmsLinkItem.parent().addClass(this.options.categoryItemSelector);

                    categoryMenuItem = cmsLinkItem;
                }

                return categoryMenuItem;
            }
        });

        return $.mage.breadcrumbs;
    };
});
