define([
    'jquery',
    'matchMedia',
    'jquery/ui'
], function ($, mediaCheck) {
    'use strict';

    $.widget('ewave.wcagHandler', {
        options: {
            focusElement: '.link',
            focusOpenedClass: '-onfocus-open',
            showHiddenForRightLeft: true,
            expandedAttribute: 'aria-expanded',
            expandedElement: '[aria-expanded]',
            parentElement: '.-parent',
            keyUp: 38,
            keyDown: 40,
            keyLeft: 37,
            keyRight: 39,
            keyEsc: 27,
            keyTab: 9,
            minScreenWidth: '768px'
        },

        useCustomLogic: false,
        tabEvent: false,
        isForceFocused: false,

        _create: function () {
            this._mediaCheck();
        },

        /**
         * Media check
         * @private
         */
        _mediaCheck: function () {
            var self = this;
            mediaCheck({
                media: '(min-width: ' + self.options.minScreenWidth + ')',
                // Switch to Desktop Version
                entry: function () {
                    self._bind();
                },

                // Switch to Mobile and Tablet Version
                exit: function () {
                    self._clearAfterResize();
                }
            });
        },

        /**
         * Bind events
         * @private
         */
        _bind: function () {
            var self = this;
            this.element.on('focusin', this.options.focusElement, function (e) {
                self.focusActived = true;
                if (self.tabEvent && !self.isForceFocused) {
                    self._tabEvents($(e.target));
                }
                self.isForceFocused = false;
            });

            this.element.on('focusout', this.options.focusElement, function (e) {
                self.focusActived = false;
                setTimeout(function () {
                    if (!self.focusActived) {
                        self._clearFocusClass();
                    }
                }, 30);
            });

            this.element.on('keydown', this.options.focusElement, $.proxy(function (e) {
                var el = $(e.target);
                switch (e.which) {
                    case this.options.keyUp:
                        if (this.useCustomLogic) {
                            this._prevFocusElement(el, e);
                        }
                        break;
                    case this.options.keyDown:
                        this._nextFocusElement(el, e);
                        break;
                    case this.options.keyLeft:
                        this._moveLeftDirection(el, e);
                        break;
                    case this.options.keyRight:
                        this._moveRightDirection(el, e);
                        break;
                    case this.options.keyEsc:
                        this._clearFocusClass();
                        break;
                    case this.options.keyTab:
                        this.tabEvent = true;
                        this.tabShiftEvent = e.shiftKey;
                        this._checkTabLogic(el);
                        break;
                }
            }, this));

            this.element.on('mouseenter', this.options.parentElement, function (e) {
                self._changeAttrByHover($(this), true);
            });

            this.element.on('mouseleave', this.options.parentElement, function (e) {
                self._changeAttrByHover($(this), false);
            });
        },

        /**
         * Find next element and set focus
         * @param el
         * @param e
         * @private
         */
        _nextFocusElement: function (el, e) {
            var childLevel = this._findChildLevel(el),
                focusElement, nextElement;

            this._clearFocusClass(el);

            if (childLevel) {
                el.parent().addClass(this.options.focusOpenedClass);
                focusElement = this._findFirstFocusElement(childLevel);
                this.useCustomLogic = true;
                this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._nextFocusElement(focusElement, e);
            } else {
                nextElement = this._findNextElement(el.parent());
                if (nextElement) {
                    focusElement = this._findFirstFocusElement(nextElement);
                    this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._nextFocusElement(focusElement, e);
                } else {
                    this._findNextSiblingElement(el.parent(), e);
                }
            }
            e.preventDefault();
        },

        /**
         * Trigger focus event
         * @param el
         * @private
         */
        _triggerFocus: function (el) {
            this.isForceFocused = true;
            el.focus();
            this._changeAttrByKey(el);
        },

        /**
         * Find next sibling element
         * @param el
         * @param e
         * @private
         */
        _findNextSiblingElement: function (el, e) {
            var topLevel = el.closest('.' + this.options.focusOpenedClass),
                nextElem, focusElement;
            if (topLevel.length) {
                nextElem = this._findNextElement(topLevel);
                topLevel.removeClass(this.options.focusOpenedClass);
                if (nextElem) {
                    focusElement = this._findFirstFocusElement(nextElem);
                    this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._nextFocusElement(focusElement, e);
                } else {
                    this._findNextSiblingElement(topLevel, e);
                }
            } else {
                nextElem = this._findNextElement(el.parent());
                if (nextElem) {
                    focusElement = this._findFirstFocusElement(nextElem);
                    this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._nextFocusElement(focusElement, e);
                }
            }
        },

        /**
         * Find previous element and st focus
         * @param el
         * @param e
         * @private
         */
        _prevFocusElement: function (el, e) {
            var prevElement = this._findPrevElement(el.parent()),
                focusElement, childLevel;
            if (prevElement) {
                focusElement = this._findFirstFocusElement(prevElement);
                childLevel = this._findChildLevel(focusElement);

                if (childLevel) {
                    prevElement.addClass(this.options.focusOpenedClass);
                    focusElement = this._findFirstFocusElement(this._findLastElement(childLevel));
                }
                if (focusElement.length) {
                    this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._prevFocusElement(focusElement, e);
                } else {
                    this._findPrevSiblingElement(el.parent(), e);
                }
            } else {
                this._findPrevSiblingElement(el.parent(), e);
            }

            if (e) {
                e.preventDefault();
            }
        },

        /**
         * Find previous sibling element and set focus
         * @param el
         * @param e
         * @private
         */
        _findPrevSiblingElement: function (el, e) {
            var topLevel = el.closest('.' + this.options.focusOpenedClass),
                focusElement, prevElement;
            if (topLevel.length) {
                focusElement = this._findFirstFocusElement(topLevel);
                this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._prevFocusElement(focusElement, e);
                topLevel.removeClass(this.options.focusOpenedClass);
                this._changeAttrByKey(focusElement);
            } else {
                prevElement = this._findPrevElement(el.parent());
                if (prevElement) {
                    focusElement = this._findFirstFocusElement(prevElement.children().last());
                    this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._prevFocusElement(focusElement, e);
                }
            }
        },

        /**
         * Move to left direction
         * @param el
         * @param e
         * @private
         */
        _moveLeftDirection: function (el, e) {
            var topLevel = this._findTopLevel(el.parent()),
                prevElement,
                focusElement;

            $('.' + this.options.focusOpenedClass).removeClass(this.options.focusOpenedClass);
            if (topLevel) {
                focusElement = this._findFirstFocusElement(topLevel);
                this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._moveLeftDirection(focusElement, e);
            } else {
                prevElement = this._findPrevElement(el.parent());
                if (prevElement) {
                    focusElement = this._findFirstFocusElement(prevElement);
                    this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._moveLeftDirection(focusElement, e);
                } else {
                    this._findPrevSiblingElement(el.parent(), e);
                }
            }

            this._showHiddenLevel(focusElement);
        },

        /**
         * Move to right direction
         * @param el
         * @param e
         * @private
         */
        _moveRightDirection: function (el, e) {
            var topLevel = this._findTopLevel(el.parent()),
                nextElement,
                focusElement;

            $('.' + this.options.focusOpenedClass).removeClass(this.options.focusOpenedClass);
            if (topLevel) {
                focusElement = this._findFirstFocusElement(topLevel);
                this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._moveRightDirection(focusElement, e);
            } else {
                nextElement = this._findNextElement(el.parent());
                if (nextElement) {
                    focusElement = this._findFirstFocusElement(nextElement);
                    this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._moveRightDirection(focusElement, e);
                } else {
                    this._findNextSiblingElement(el.parent(), e);
                }
            }
            this._showHiddenLevel(focusElement);
        },

        /**
         * Show hidden level
         * @param el
         * @private
         */
        _showHiddenLevel: function (el) {
            if (this.useCustomLogic && this.options.showHiddenForRightLeft && el && this._findChildLevel(el)) {
                el.parent().addClass(this.options.focusOpenedClass);
                this._changeAttrByKey(el);
            }
        },

        /**
         * Check element for possibility adding focus event
         * @param el
         * @return {el or boolean}
         * @private
         */
        _isTabbable: function (el) {
            return el.is(':tabbable') ? el : false;
        },

        /**
         * Checking the nesting
         * @param el
         * @return {boolean or string} Boolean or element
         * @private
         */
        _findChildLevel: function (el) {
            return el.next().length ? el.next() : false;
        },

        /**
         * Checking that element is in child level
         * @param el
         * @return {boolean}
         * @private
         */
        _isChildLevel: function (el) {
            return !!el.parent().closest('.' + this.options.focusOpenedClass).length;
        },

        /**
         * Find the next element
         * @param el
         * @return {boolean or string} Boolean or next element
         * @private
         */
        _findNextElement: function (el) {
            return el.next().length ? el.next() : false;
        },

        /**
         * Find the previous element
         * @param el
         * @return {boolean or string} Boolean or previous element
         * @private
         */
        _findPrevElement: function (el) {
            return el.prev().length ? el.prev() : false;
        },

        /**
         * Find focus element
         * @param el
         * @private
         */
        _findFirstFocusElement: function (el) {
            return el ? el.find(this.options.focusElement).filter(':visible').first() : false;
        },

        /**
         * Find last focus element
         * @param el
         * @private
         */
        _findLastElement: function (el) {
            return el.children().children().last();
        },

        /**
         * Remove focused class
         * @param el
         * @private
         */
        _clearFocusClass: function (el) {
            var focusContainer = el ? el.parent().parent() : this.element;
            focusContainer.find('.' + this.options.focusOpenedClass).removeClass(this.options.focusOpenedClass);
            if (!el) {
                this.useCustomLogic = false;
                this.element.find(this.options.expandedElement).attr(this.options.expandedAttribute, false);
            } else {
                focusContainer.find(this.options.expandedElement).attr(this.options.expandedAttribute, false);
            }
        },

        /**
         * Unbid events
         * @private
         */
        _clearAfterResize: function () {
            this.element.off('keydown', this.options.focusElement);
            this.useCustomLogic = false;
        },

        /**
         * Find parent level
         * @param el
         * @return {boolean}
         * @private
         */
        _findParentLevel: function (el) {
            var prentLevel = el.closest('.' + this.options.focusOpenedClass);
            return prentLevel.length ? prentLevel : false;
        },

        /**
         * Find top level
         * @param el
         * @return {boolean}
         * @private
         */
        _findTopLevel: function (el) {
            var topLevel = el.parents('.' + this.options.focusOpenedClass);
            return topLevel.length ? topLevel.last() : false;
        },

        /**
         * Tab behavior
         * @param el
         * @private
         */
        _tabEvents: function (el) {
            var parents = el.parents('.' + this.options.focusOpenedClass),
                openedElements = $('.' + this.options.focusOpenedClass).not(parents);
            if (openedElements.length) {
                openedElements.removeClass(this.options.focusOpenedClass);
            }

            if (this.useTabCustomLogic) {
                this._showHiddenLevel(el);
                if (this.tabShiftEvent) {
                    this._setFocusLastElement(el);
                }
            }
            this.tabEvent = false;
            this._changeAttrByKey(el);
        },

        /**
         * Check that active element is last
         * @param level
         * @param id
         * @return {boolean}
         * @private
         */
        _checkLastChild: function (level, id) {
            var lastVisibleId = level.find(this.options.focusElement).filter(':visible').last().attr('id');
            return lastVisibleId === id;
        },

        /**
         * Check that active element is first
         * @param level
         * @param id
         * @return {boolean}
         * @private
         */
        _checkFirstChild: function (level, id) {
            var firstVisibleId = level.find(this.options.focusElement).filter(':visible').first().attr('id');
            return firstVisibleId === id;
        },

        /**
         * Logic for tab events
         * @param el
         * @param shift
         * @private
         */
        _checkTabLogic: function (el) {
            var topLevel = this._findTopLevel(el);
            this.useTabCustomLogic = this.tabShiftEvent ? !!(topLevel && this._checkFirstChild(topLevel, el.attr('id'))) : !!(topLevel && this._checkLastChild(topLevel, el.attr('id')));
        },

        /**
         * Find last tabble element end trigger focus event
         * @param el
         * @private
         */
        _setFocusLastElement: function (el) {
            var parent = el.closest('.' + this.options.focusOpenedClass),
                focusElement = parent.find(this.options.focusElement).filter(':visible').last();
            this._isTabbable(focusElement) ? this._triggerFocus(focusElement) : this._prevFocusElement(focusElement);
        },

        /**
         * Change target attribute by the hover event
         * @param el
         * @param isAdd
         * @private
         */
        _changeAttrByHover: function (el, isAdd) {
            var expandedElements = this._findTargetExpandedElements(this._findExpandedParents(el, this.options.parentElement));
            this.element.find(this.options.expandedElement).not(expandedElements).attr(this.options.expandedAttribute, false);
            el.children(this.options.expandedElement).attr(this.options.expandedAttribute, !!isAdd);
        },

        /**
         * Change target attribute by keyboard events
         * @param el
         * @param isAdd
         * @private
         */
        _changeAttrByKey: function (el) {
            var expandedElements = this._findTargetExpandedElements(this._findExpandedParents(el, '.' + this.options.focusOpenedClass));
            this.element.find(this.options.expandedElement).not(expandedElements).attr(this.options.expandedAttribute, false);
            expandedElements.attr(this.options.expandedAttribute, true);
        },

        /**
         * Find all  the elements that have hidden submenu relative to the active element
         * @param el
         * @private
         */
        _findExpandedParents: function (el, targetSelector) {
            return el.parents(targetSelector);
        },

        /**
         * Find all elements that have target attribute
         * @param boxes
         * @private
         */
        _findTargetExpandedElements: function (boxes) {
            return boxes.children(this.options.expandedElement);
        }
    });

    return $.ewave.wcagHandler;
});
