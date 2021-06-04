import $ from 'jquery';
import mageTemplate from 'mage/template';

export default class View {
    constructor ({options, element, ..._store}) {
        this.options = Object.assign({}, this.options, options);
        this.element = element;
        this.Store = _store.Store;
        this.Events = _store.Events;
        this.previousSort = [];
        this.bind();
        this.watchers();
    }

    watchers () {
        this.Store.on(this.Events.RELATED_INITIALIZED, (data) => this.initialized(data));
        this.Store.on(this.Events.OPEN_DETAILS, (data) => this.open(data));
        this.Store.on(this.Events.OPENED_DETAILS, (data) => this.opened(data));
        this.Store.on(this.Events.CLOSE_DETAILS, (data) => this.close(data));
        this.Store.on(this.Events.CLOSED_DETAILS, (data) => this.closed(data));
        this.Store.on(this.Events.TOGGLE_DETAILS, (data) => this.toggle(data));
        this.Store.on(this.Events.FILTERED, (data) => this.filtered(data));
    }

    bind () {
        this.initializeFilters();
        this.initializeDetailsExpander();
        this.Store.emit(this.Events.RELATED_INITIALIZED, this.element);
    }

    /**
     * Initialize filter/sorter module
     */
    initializeFilters () {
        let self = this;

        if (this.options.filterable) {
            require(['shuffleExtend'], (Shuffle) => {
                self.customizeShuffle(Shuffle);
                self.shuffle = new Shuffle(document.getElementById(self.options.gridId), self.setShuffleOptions(Shuffle));
                if (self.options.initialFilter) {
                    self.applySort(self.options.initialFilter);
                }
                self.setFilterItems();
            });
        }
    }

    /**
     * Customize default Shuffle styles and classes
     * @param module
     */
    customizeShuffle (module) {
        module.ShuffleItem.Css.INITIAL = {};
        module.ShuffleItem.Css.VISIBLE.before = {};
        module.ShuffleItem.Css.HIDDEN.before = {};
        module.ShuffleItem.Css.HIDDEN.after = {};
        module.Classes.VISIBLE = '-visible';
        module.Classes.HIDDEN = '-hidden';
    }

    /**
     * Set Shuffle Options
     * @param Shuffle
     * @returns {{group: string, itemSelector: string}}
     */
    setShuffleOptions (Shuffle) {
        return {
            group: this.options.initialFilter ? this.options.initialFilter : Shuffle.ALL_ITEMS,
            itemSelector: '.product-item'
        };
    }

    /**
     * Attach listener for filters
     */
    setFilterItems () {
        if (!this.options.filterItems) {
            return;
        }
        $(this.options.filterItems).on('click', (e) => this.applyFilter(e));
    }

    /**
     * Apply selected filter
     * @param e
     */
    applyFilter (e) {
        let element = e.currentTarget,
            $element = $(element),
            elementGroup = element.getAttribute('data-group');

        if (!$element.hasClass(this.options.filterActiveState)) {
            this.Store.emit(this.Events.CLOSE_DETAILS, $(`${this.options.expanderElement}.-active`));
            $element.addClass(this.options.filterActiveState).siblings().removeClass(this.options.filterActiveState);

            this.toggleFilterItemsInfo(elementGroup);

            this.shuffle.filter(elementGroup);
            this.applySort(elementGroup);
            this.Store.emit(this.Events.FILTERED, $element);
        }
    }

    toggleFilterItemsInfo (categoryId) {
        $(`${this.options.filterInfo}[data-category-id="${categoryId}"]`).addClass(this.options.filterActiveState).siblings('[data-category-id]').removeClass(this.options.filterActiveState);
    }

    /**
     * Apply sort by selected filter
     * @param group
     */
    applySort (group) {
        let self = this,
            elementsArray = [],
            positionArray = [];
        $.each(this.shuffle.items, function (index, item) {
            elementsArray.push(item.element);
            positionArray.push(self.getPosition(item.element, group));
        });

        if (this.isEqualArrays(positionArray, this.previousSort)) {
            return;
        } else {
            this.previousSort = positionArray;
        }

        elementsArray.sort((a, b) => {
            return self.getPosition(a, group) - self.getPosition(b, group);
        });

        $(`#${this.options.gridId}`).html(elementsArray);
    }

    /**
     * Get position of product by category
     * @param element
     * @param group
     * @returns {Number}
     */
    getPosition (element, group) {
        let index = JSON.parse(element.getAttribute('data-groups')).indexOf(group);
        if (index === -1) {
            index = 0;
        }
        return parseInt(element.getAttribute('data-sort-order').split(',')[index], 10);
    }

    /**
     * Check equal arrays
     * @param a
     * @param b
     * @returns {boolean}
     */
    isEqualArrays (a, b) {
        if (a.length != b.length) {
            return false;
        }
        for (let i = 0; i < a.length; ++i) {
            if (a[i] !== b[i]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Filter applied
     * @param $element
     */
    filtered ($element) {

    }

    /**
     * Attach listener for expanders
     */
    initializeDetailsExpander () {
        if (this.options.expandable) {
            $(document).on('click', this.options.expanderElement, (e) => {
                e.preventDefault();
                this.Store.emit(this.Events.TOGGLE_DETAILS, $(e.currentTarget));
            });
        }
    }

    /**
     * Module initialized
     * @param $element
     */
    initialized ($element) {

    }

    /**
     * Toggle details expander
     * @param $element
     * @param state
     */
    toggle ($element, state) {
        if ($element.hasClass('-active')) {
            this.Store.emit(this.Events.CLOSE_DETAILS, $element);
        } else {
            this.Store.emit(this.Events.OPEN_DETAILS, $element);
        }
    }

    /**
     * Update text of switch
     * @param element
     */
    updateSwitchText (element) {
        let moreText = element.data('text-more'),
            lessText = element.data('text-less');

        if (element.hasClass('-active')) {
            element.attr('title', lessText);
            element.text(lessText);
        } else {
            element.attr('title', moreText);
            element.text(moreText);
        }
    }

    /**
     * Open details of product
     * @param $element
     */
    open ($element) {
        if (!$($element.data('expander')).length) {
            console.warn('Expander container not found!');
            return;
        }

        let $previous = $element.siblings('.-active');

        this.removeContent($element);
        $element.addClass('-active');
        $element.find(this.options.switchElement).addClass('-active');
        $previous.removeClass('-active').find(this.options.switchElement).removeClass('-active');
        if (this.options.switchElement) {
            this.updateSwitchText($previous.find(this.options.switchElement));
            this.updateSwitchText($element.find(this.options.switchElement));
        }
        this.addContent($element);
        if (this.options.scroll) {
            this.scroll($element);
        }
        this.Store.emit(this.Events.OPENED_DETAILS, $element);
    }

    /**
     * Expander opened
     */
    opened () {

    }

    /**
     * Close details of product
     * @param $element
     */
    close ($element) {
        this.removeContent($element);
        $element.removeClass('-active');
        $element.find(this.options.switchElement).removeClass('-active');
        if (this.options.switchElement) {
            this.updateSwitchText($element.find(this.options.switchElement));
        }
        this.Store.emit(this.Events.CLOSED_DETAILS, $element);
    }

    /**
     * Expander closed
     */
    closed () {

    }

    /**
     * Insert expander holder after selected product
     * @param $element
     */
    addContent ($element) {
        let expanderTemplate = mageTemplate(this.options.expanderTemplate);

        $(expanderTemplate({
            data: {
                html: $($element.data('expander')).html()
            }
        })).insertAfter($element);
    }

    /**
     * Remove expander
     * @param $element
     */
    removeContent ($element) {
        $element.siblings(this.options.expanderHolder).remove();
    }

    /**
     * Scroll to details || selected product
     * @param $element
     */
    scroll ($element) {
        let offset = (this.options.scrollTo === 'details') ? $(this.options.expanderHolder).offset().top : $element.offset().top;
        if (offset) {
            $('html, body').animate({
                scrollTop: offset
            }, 500);
        }
    }
}
