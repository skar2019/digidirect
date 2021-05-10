import $ from 'jquery';
import {Store, Events} from './../common/store';
import mediaCheck from 'matchMedia';

export default class View {
    constructor (options) {
        this.options = Object.assign({}, this.options, options);
        this.watchers();
        this.bind();
    }

    /**
     * Watchers
     */
    watchers () {
        Store.on(Events.DATA_FETCH_PROGRESS, () => this.progress());
        Store.on(Events.DATA_FETCH_SUCCESS, (data) => this.success(data));
        Store.on(Events.FAQ_SEARCH, (data) => this.search(data));
        Store.on(Events.FAQ_ITEM_TOGGLE, (data) => this.toggle(data));
        Store.on(Events.FAQ_COMPACT_MODE_ON, (data) => this.onCompactMode(data));
        Store.on(Events.FAQ_COMPACT_MODE_OFF, (data) => this.offCompactMode(data));
        Store.on(Events.ERROR, (data) => this.error(data));
    }

    bind () {
        this.initSearch();
        this.initActions();
        this.toggleQuestion();
        this.goBack();
        this.initCompactMode();
        this.showAllTags();
        this.initLoadNextPage();
        this.initQuestionForm();
    }

    /**
     * Initialization of search
     */
    initSearch () {
        $(this.options.searchForm).on('submit', function () {
            Store.emit(Events.FAQ_SEARCH, $(this));
            return false;
        });
    }

    /**
     * Initialization of actions
     */
    initActions () {
        $(this.options.actionLinks).on('click', (e) => {
            let $this = $(e.currentTarget),
                url = $this.attr('href');
            e.preventDefault();
            if (url === undefined) {
                url = this.options.baseUrl;
            }
            this.loadCategoryItems($this, url);
        });
    }

    /**
     * Render content
     * @param data
     * @private
     */
    _renderData (data) {
        $(this.options.list).html(data.content);
    }

    /**
     * Search FAQs
     * @param form
     */
    search (form) {
        if (form.valid()) {
            this.goForward();
            $(this.options.actionLinks).removeClass('-active');
            let params = '?faqType=search' + '&faqId=' + $(this.options.searchField).val() + '&page=1';
            Store.emit(Events.DATA_FETCH_START, this.options.url + params, this.options.baseUrl);
        }
    }

    /**
     * Load items of selected category
     * @param category
     * @param realUrl
     */
    loadCategoryItems (category, realUrl = false) {
        let $this = category,
            type = $this.data('type'),
            categoryId = $this.data('category-id'),
            page = $this.data('page');

        this.goForward();

        // Filling input:hidden
        $('#faqtype').value = $this.data('type');
        $('#faqid').value = $this.data('category-id');

        if (!$this.hasClass('-active')) {
            $(this.options.actionLinks).removeClass('-active');
            $this.addClass('-active');
            let params = '?faqType=' + type + '&faqId=' + categoryId + '&page=' + page;
            Store.emit(Events.DATA_FETCH_START, this.options.url + params, realUrl);
        }
    }

    toggleQuestion () {
        $(document).on('click', this.options.questions, function (e) {
            e.preventDefault();
            Store.emit(Events.FAQ_ITEM_TOGGLE, $(this).data('question-id'));
        });
    }

    /**
     * Toggle FAQ answer
     * @param id
     */
    toggle (id) {
        let $item = $('#faq-question-' + id).closest(this.options.items);

        if ($item.hasClass('-active')) {
            $item.removeClass('-active');
        } else {
            if (!this.options.multipleCollapsible) {
                $item.siblings(this.options.items).removeClass('-active');
            }
            $item.addClass('-active');
        }
    }

    /**
     * Go to FAQ topic via add class attribute if Compact Mode enabled
     */
    goForward () {
        $(this.options.container).addClass('-active');
    }

    /**
     * Go back to categories if Compact Mode enabled
     */
    goBack () {
        $(document).on('click', this.options.backItem, () => this.goToCategories());
    }

    /**
     * Go to categories via remove class attribute
     * Specially for CSS
     */
    goToCategories () {
        $(this.options.container).removeClass('-active');
    }

    /**
     * Show all tags
     */
    showAllTags () {
        $(document).on('click', this.options.toggleTags, (e) => {
            $(e.currentTarget).addClass('-hide');
            $(this.options.extraTags).addClass('-show');
        });
    }

    /**
     * Initialization load FAQs clicking on the pagination
     */
    initLoadNextPage () {
        $(document).on('click', this.options.nextPage, (e) => {
            e.preventDefault();
            let urlParams = $(e.currentTarget).attr('href').split('?')[1],
                realUrl,
                pageParamName = 'p',
                pageParamValue;

            if (urlParams) {
                urlParams = '?' + urlParams;
            } else {
                urlParams = '';
            }

            pageParamValue = this.getParameterByName(pageParamName, this.options.url + urlParams);

            realUrl = this.removeEndSymbol(window.location.protocol + '//' + window.location.host + window.location.pathname, '/') + '?' + pageParamName + '=' + pageParamValue;

            Store.emit(Events.DATA_FETCH_START, this.options.url + urlParams, realUrl);
        });
    }

    getParameterByName (name, url) {
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) {
            return null;
        }
        if (!results[2]) {
            return '';
        }
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    removeEndSymbol (string, symbol) {
        var position = string.lastIndexOf(symbol);
        if (position == string.length - symbol.length) {
            string = string.substr(0, position);
        }
        return string;
    }

    /**
     * Compact Mode initialization
     */
    initCompactMode () {
        if (this.options.compactModeBreakpoint) {
            mediaCheck({
                media: `(min-width: ${this.options.compactModeBreakpoint})`,
                entry: () => Store.emit(Events.FAQ_COMPACT_MODE_OFF),
                exit: () => Store.emit(Events.FAQ_COMPACT_MODE_ON)
            });
        }
    }

    /**
     * Compact Mode enabled
     */
    onCompactMode () {
        this.goToCategories();
    }

    /**
     * Compact Mode disabled
     */
    offCompactMode () {
    }

    /**
     * Initialization of question form
     */
    initQuestionForm () {
        $(document).on('click', this.options.questionButton, () => {
            $(this.options.questionContainer).addClass('-active');
        });
        $(this.options.questionForm).on('submit', () => {
            if ($(this.options.questionForm).valid()) {
                $(this.options.questionFormSubmit).attr('disabled', true);
            }
        });
    }
    
    progress () {
        $(this.options.container).loader().trigger('processStart');
    }

    success (data) {
        this._renderData(data);
        $(this.options.container).loader().trigger('processStop');
    }

    error () {
    }
}
