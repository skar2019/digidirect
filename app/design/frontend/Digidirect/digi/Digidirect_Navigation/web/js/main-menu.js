define([
    'jquery',
    'mage/translate',
    'mage/template',
    'text!Digidirect_Navigation/template/view-all.html',
    'jquery/ui'
], function ($, $t, mageTemplate, viewAllTmpl) {
    'use strict';

    $.widget('Digidirect.mainMenu', {
        options: {
            secondaryMenu: '.menu-sec',
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
            this.addViewAll();
            this.cloneCMS();
            this.secMenu();
            this.firstStep();
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
            if (!window.cloneCmsMenuFlag && $(this.options.cmsCloneSelector).length) {
                var $clone = $(this.options.cmsCloneSelector).clone();
                $clone.find('a').attr('rel', 'nofollow');
                $clone.appendTo('.menu-wrapper > .item > .sub-menu');
                window.cloneCmsMenuFlag = true;
            }
        },

        secMenu: function () {

            var that = this;

            $('.nav-offcanvas .category-item').on('click', function () {
                if (this.querySelector(that.options.secondaryMenu).classList.contains('-clear')) {
                    this.querySelector(that.options.secondaryMenu).classList.remove('-clear');
                    this.querySelector('.link').classList.remove('-min');
                } else {
                    this.querySelector(that.options.secondaryMenu).classList.add('-clear');
                    this.querySelector('.link').classList.add('-min');
                }
            })
        },

        firstStep: function () {
            $('.action.nav-toggle').on('click', function () {
                $('.menu-back').trigger('click')
            })
        }
    });

    return $.Digidirect.mainMenu;
});
