define([
    'jquery',
    'mage/translate',
    'mage/template',
    'text!Ewave_Navigation/template/back.html',
    'text!Ewave_Navigation/template/view-all.html',
    'jquery/ui'
], function ($, $t, mageTemplate, menuBackTmpl, viewAllTmpl) {
    'use strict';

    $.widget('ewave.mainMenu', {
        options: {
            menuSectionSelector: '.menu-section',
            slideClassName: '-slide',
            subSlideClassName: '-sub-slide',
            menuBackTitle: $t('Main Menu'),
            viewAllTitle: $t('View All'),
            viewAllStep: 10,
            viewAllAffect: '_hide-item',
            cmsCloneSelector: '.menu-cmsitem'
        },
        _create: function () {
            this.addMenuBack();
            this.addViewAll();
            this.cloneCMS();
            this._bind();
        },

        _bind: function () {
            var self = this;

            $('[data-action="toggle-nav"], .navigation-wrapper > .menu-back').on('click', function () {
                $(self.options.menuSectionSelector).removeClass(self.options.slideClassName).removeClass(self.options.subSlideClassName);
            });

            this.element.on('click', function () {
                $(self.options.menuSectionSelector).addClass(self.options.slideClassName);
            });
        },

        addMenuBack: function () {
            var tmpl = mageTemplate(menuBackTmpl, {
                name: this.options.menuBackTitle
            });

            $(tmpl).prependTo($('.navigation-wrapper'));
        },

        addViewAll: function () {
            var self = this,
                tmpl = mageTemplate(viewAllTmpl, {
                    name: this.options.viewAllTitle
                });

            $('.navigation-wrapper .sub-menu.-level1 .cms, .navigation-wrapper .sub-menu.-level1 .item.-type-category').each(function () {
                var $this = $(this);
                if ($this.find('li > a').length > self.options.viewAllStep) {
                    $(tmpl).appendTo($this);

                    $this.find('li > a').each(function (i, el) {
                        if (i >= self.options.viewAllStep) {
                            $(el).closest('li').addClass(self.options.viewAllAffect);
                        }
                    });
                }
            });

            $('[data-role="menu-category-switcher"]').on('click', function () {
                $(this).closest('.cms, .item.-type-category').find('.' + self.options.viewAllAffect).removeClass(self.options.viewAllAffect);
                $(this).hide();
            });
        },
        
        cloneCMS: function () {
            if ($(this.options.cmsCloneSelector).length) {
                var $clone = $(this.options.cmsCloneSelector).clone();
                $clone.find('a').attr('rel', 'nofollow');
                $clone.appendTo('.menu-wrapper > .item > .sub-menu');
            }
        }
    });

    return $.ewave.mainMenu;
});
