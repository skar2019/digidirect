define([
    'jquery',
    'mage/collapsible',
    'jquery/ui',
    'jquery/jquery-storageapi',
    'mage/mage'
], function ($) {
    'use strict';

    $.widget('mage.collapsible', $.mage.collapsible, {
        options: {
            closeOnClickOutside: false,
            closeOnClickInside: false,
            toggleHeaderText: false,
            toggleHeaderActive: '-active',
            headerTextContainer: '[data-role="title"]',
            newHeaderText: '[data-header]',
            toggleContainer: false,
            toggleContainerActive: '-active',
            toggleContainerAction: '[data-header]'
        },
        _create: function () {
            this._super();
            this._updateHeader();
            this._toggleContainer();
        },
        _open: function () {
            this._super();
            this._bindEvent();
        },
        _close: function () {
            this._super();
            this._unbindEvent();
        },
        _bindEvent: function () {
            if (this.options.closeOnClickOutside) {
                $(document).on('click.outsideCollapsible', this._clickOutside.bind(this));
            }
            if (this.options.closeOnClickInside) {
                $(document).on('click.insideCollapsible', this._clickInside.bind(this));
            }
        },
        _clickOutside: function (event) {
            if (!this.element.is(event.target) && this.element.has(event.target).length === 0) {
                this.deactivate();
            }
        },
        _clickInside: function (event) {
            if (this.content.is(event.target) || this.content.has(event.target).length !== 0) {
                this.deactivate();
            }
        },
        _unbindEvent: function () {
            $(document).off('click.outsideCollapsible click.insideCollapsible');
        },
        _updateHeader: function () {
            if (this.options.toggleHeaderText) {
                var self = this;
                this._on(this.element.find(this.options.newHeaderText), {
                    click: function (e) {
                        var $this = $(e.currentTarget),
                            value = $this.data('header'),
                            newText;
                        if (value) {
                            newText = value;
                        } else {
                            newText = $this.text();
                        }
                        if (self.element.find(self.options.headerTextContainer).length) {
                            self.element.find(self.options.headerTextContainer).text(newText);
                        } else {
                            self.header.text(newText);
                        }
                    }
                });
            }
        },
        _toggleContainer: function () {
            if (this.options.toggleContainer) {
                var self = this;
                this._on(this.element.find(this.options.toggleContainerAction), {
                    click: function (e) {
                        e.preventDefault();
                        var $this = $(e.currentTarget),
                            href = $this.attr('href');
                        if (!$this.hasClass(self.options.toggleHeaderActive)) {
                            $this.addClass(self.options.toggleHeaderActive).siblings().removeClass(self.options.toggleHeaderActive);
                            if (href && $(href).length) {
                                $(href).addClass(self.options.toggleContainerActive).siblings().removeClass(self.options.toggleContainerActive);
                                self.element.trigger('collapsible.toggle.container', href);
                            }
                        }
                    }
                });
            }
        }
    });

    return $.mage.collapsible;
});
