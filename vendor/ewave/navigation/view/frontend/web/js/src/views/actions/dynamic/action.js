import $ from 'jquery';
import ko from 'knockout';

/** Class representing dynamic functionality of commponent */
export default class Action {
    /**
     * Initialize component's viewmodel
     * @param options
     * @param viewCore
     */
    constructor(options, viewCore) {
        let self = this;
        this.options = Object.assign({}, this.options, options);

        this.viewModel = function (options) {
            this.wrapperClass = ko.observable(`${options.horizontal ? ' -horizontal' : ''} ${options.expanded ? ' -expanded' : ''}`);

            $(options.area).addClass(options.responsive ? '-responsive' : '');

            ({
                'menu': this.menu,
                'action': this.action,
                'responsive': this.responsive
            } = options);

            this.action = ko.observable(this.action);

            this.toggleInner = (item, event) => {
                viewCore.toggleInner(event, this.options, this);
                let target = $(event.target);
                if (!(!!target.siblings().find(options.innerListsClass).length || !!target.siblings().find(options.cmsBlockClassName).length)) {
                    return true;
                }
            };

            this._collapseAll = viewCore._collapseAll;

            this._isLink = (item) => item['is_link'] === true || item['is_link'] === '1';

            this.displayMode = (item) => this._isLink(item) ? 'Ewave_Navigation/common/link' : 'Ewave_Navigation/common/span';
        };

        this.vm = new this.viewModel(self.options);

        if (this.options.action === 'click') {
            $(window).on('click',() => {
                $(options.subMenuBlockClass + '.-open').removeClass('-open');
            });
        }

        this._renderTemplate();
    }

    /**
     * renders knockout template in selected area and binds viewmodel to this area
     * @private
     */
    _renderTemplate() {
        var self = this;
        let template,
            path = 'text!Ewave_Navigation/template/view/action.html';
        try {
            require([path], (action) => {
                template = action;
                $(template).appendTo(this.options.area);
                ko.applyBindings(self.vm, $(this.options.area)[0]);
            });
        } catch (e) {
            console.warn("Navigation: Template hasn't been loaded");
        }
    }
}