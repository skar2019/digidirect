import $ from 'jquery';
import mediaCheck from 'matchMedia';
import {Store, Events} from './../../../common/store';

/** Class defining offcanvas functionality */
export default class Offcanvas {
    /**
     * initialize offcanvas logic
     * @param options
     * @param vm
     */
    constructor (options, vm) {
        this.options = Object.assign({}, this.options, options);
        this.watchers();
        this.bind(vm);
    };

    /**
     * Binds Global Store Events to methods of class
     */
    watchers () {
        Store.on(Events.OFFCANVAS_OFF, (data) => this.offCanvasDisable(data));
        Store.on(Events.OFFCANVAS_ON, (data) => this.offCanvasEnable(data));
    };

    /**
     * Enable off-canvas
     * @param view
     * @private
     */
    offCanvasEnable (view) {
        if (view.options.action !== 'click') {
            view._toggleAction('click');
        }
        let navigation = $(this.options.area + ' ' + this.options.wrapperClass);
        if (navigation.hasClass('-horizontal')) {
            navigation.removeClass('-horizontal');
        }
        if (!navigation.hasClass('-expanded')) {
            navigation.addClass('-expanded');
        }
        view._collapseSub();
    };

    /**
     * Disable off-canvas
     * @param view
     * @private
     */
    offCanvasDisable (view) {
        if (view.options.action !== this.options.action) {
            view._toggleAction(this.options.action);
        }
        let navigation = $(this.options.area + ' ' + this.options.wrapperClass),
            htmlContainer = $('html');
        if (this.options.horizontal) {
            navigation.addClass('-horizontal');
        }
        if (this.options.expanded) {
            if (!navigation.hasClass('-expanded')) {
                navigation.addClass('-expanded');
            }
        } else {
            navigation.removeClass('-expanded');
        }
        view._collapseSub();
        if (htmlContainer.hasClass(this.options.offCanvasClass)) {
            htmlContainer.removeClass(this.options.offCanvasClass);
        }
    };

    /**
     * Adds events to controls selectors
     * @private
     */
    bind (vm) {
        let self = this;
        $(this.options.togglerSelector).on(this.options.offCanvasEvent, () => {
            this.toggle();
        });
        mediaCheck({
            media: `(max-width: ${self.options.breakpoint} )`,
            entry: $.proxy(function () {
                Store.emit(Events.OFFCANVAS_ON, vm);
            }, this),
            exit: $.proxy(function () {
                Store.emit(Events.OFFCANVAS_OFF, vm);
            }, this)
        });
    };

    /**
     * Toggles appearence of offcanvas menu in mobile mode
     */
    toggle () {
        let htmlContainer = $('html');
        if (htmlContainer.hasClass(this.options.offCanvasClass)) {
            htmlContainer.removeClass(this.options.offCanvasClass);
        } else {
            setTimeout(() => {
                htmlContainer.addClass(this.options.offCanvasClass);
            }, 42);
        }
    };
}
