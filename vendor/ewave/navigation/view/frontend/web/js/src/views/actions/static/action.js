import $ from 'jquery';

/**Class representing Static menu functionality */
export default class Action {
    /**
     * Initialize static functionality
     * @param options
     * @param viewCore
     */
    constructor (options, viewCore) {
        this.options = Object.assign({}, this.options, options);
        this.viewCore = viewCore;
        this._initAdditional();
        this._initAction();
    }

    /**
     * Triggers functions that defines additional classes and functionality for menu nodes
     * @private
     */
    _initAdditional = () => {
        this._activeLink();
        if (this.options.action === 'click') {
            this._addLinks();
        }
    };

    /**
     * Select what action will be used and bind it with nodes
     * @private
     */
    _initAction = () => {
        let items = $(this.options.wrapperClass + ' ' + this.options.itemClass),
            self = this;
        if (this.options.action === 'hover') {
            items.on('mouseenter', this.toggleInner);
            items.on('mouseleave', (e) => {
                self.viewCore._collapseAll(e, self.options);
            });
            $(this.options.wrapperClass).on('mouseleave', () => {
                self._collapseSub();
            });
        } else {
            items.on('click', this.toggleInner);
            $(window).on('click', this._collapseSub);
        }
    };
    /**
     * Toggles actions: click -> hover, hover -> click
     * @private
     */
    _toggleAction = (action) => {
        let items = $(this.options.wrapperClass + ' ' + this.options.itemClass);
        if (action === 'click') {
            this.options.action = 'click';
            items.off('mouseenter', this.toggleInner);
            items.off('mouseleave');
            $(this.options.wrapperClass).off('mouseleave');
            this._addLinks();
        } else {
            this.options.action = 'hover';
            items.off('click', this.toggleInner);
            $(window).off('click', this._collapseSub);
            this._removeAdditionalLinks();
        }

        this._initAction();
    };

    /**
     * Toggle inner sub-menus
     * @param event
     */
    toggleInner = (event) => {
        this.viewCore.toggleInner(event, this.options, this);
    };

    /**
     * Add class "-active" to necessary node
     * @private
     */
    _activeLink = () => {
        let element = $(`${this.options.wrapperClass} ${this.options.itemClass}.-active`),
            accestors = element.closest('.-level1').closest(this.options.itemClass);
        accestors.addClass('-active');
    };

    /**
     * Add aditional link to sub-menus, used only for click action
     * @private
     */
    _addLinks = () => {
        let links = $(`${this.options.wrapperClass} .-parent > a${this.options.itemLabelClass}`),
            menu;
        if (this.options.linkString) {
            links.each((index, element) => {
                menu = $(element).siblings().find(this.options.innerListsClass)[0];
                this.viewCore.addLink(element, menu, this.options);
            });
        }
    };

    /**
     * Remove links with class -added
     * @private
     */
    _removeAdditionalLinks = () => {
        $(`${this.options.itemClass}.-added`).remove();
    };

    _collapseSub = () => {
        $(this.options.wrapperClass + ' ' + this.options.itemClass + '.-open').removeClass('-open');
        $(this.options.subMenuBlockClass + '.-open').removeClass('-open');
    };
}