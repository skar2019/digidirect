import bindings from './actions/dynamic/bindings';
import $ from 'jquery';

export default class View {
    /**
     * Initializing View
     * @param options
     */
    constructor (options) {
        this.options = Object.assign({}, this.options, options);
        this.bind();
    }

    bind () {
        if (this.isTouchDevice()) {
            this.options.action = 'click';
        }
        this.loadAction();
    }

    /**
     * Loading of default navigation functionality
     */
    loadAction () {
        let actionString;
        actionString = this.options.static ? 'static' : 'dynamic';

        // download necessary action, depending on chosen option
        require([`./Ewave_Navigation/js/dist/views/actions/${actionString}/action`], (Action) => {
            // TODO: load action and offcanvas in parallel, not sequentially.
            this.action = new Action(this.options, View);
            if (!this.options.static) {
                bindings(this.options, this.action, View);
            }
            this.loadExtraLogic();
        });
    };

    /**
     * Loading extra functionality, that can be enabled/disabled in options
     */
    loadExtraLogic () {
        // downloading and initializing of offcanvas logic if it was enabled
        if (this.options.responsive) {
            this.loadOffCanvas();
        }
    };

    /**
     * Loading Off-canvas functionality
     */
    loadOffCanvas () {
        const OFFCANVAS_PATH = 'Ewave_Navigation/js/dist/views/actions/common/offcanvas';
        require([`./${OFFCANVAS_PATH}`], (Offcanvas) => {
            this.offcanvas = new Offcanvas(this.options, this.action);
        });
    };

    /**
     * Static method defining toggling of sub-menus
     * Used in toggleInner functions of dynamic and static
     * @param event
     * @param options
     * @param self
     */
    static toggleInner (event, options, self) {
        let target = $(event.target),
            targetId,
            $menu = target.closest(options.area),
            $subMenu,
            menuItem;

        if (target.not(options.itemLabelClass)) {
            target = target.closest(options.itemLabelClass);
        }

        targetId = target.attr('id');
        $subMenu = $menu.find('[data-id="' + targetId + '"]');
        menuItem = target.closest(options.itemClass);       

        menuItem.siblings(options.itemClass).removeClass('-open');
        menuItem.siblings(options.itemClass).find(options.subMenuBlockClass).removeClass('-open');

        if ($subMenu.length) {
            event.preventDefault();
            if ($subMenu.hasClass('-open')) {
                menuItem.removeClass('-open');
                $subMenu.removeClass('-open');
            } else {
                menuItem.addClass('-open');
                $subMenu.addClass('-open');
            }
        }

        event.stopPropagation();
    };

    /**
     * Static method defining functionality, which adds links to sub menus when click action is chosen
     * Used in static and dynamic
     * @param element
     * @param menu
     * @param options
     */
    static addLink (element, menu, options) {
        let $element = $(element),
            copiedLi = $($element.closest(options.itemClass)[0]).clone(),
            copiedLiClasses = options.itemClass.replace('.', '') + ' -added ' + $(menu).closest(options.subMenuBlockClass).data('level-dip'),
            copiedLink = document.createElement('a'),
            str = options.linkString.replace('{original}', `${$element.html()}`);

        $(copiedLink).attr({
            class: options.itemLabelClass.replace('.', ''),
            href: $element.attr('href'),
            title: $(`<p>${str}</p>`).text()
        }).html(str);
        copiedLi.removeAttr('class').addClass(copiedLiClasses);
        copiedLi.html(copiedLink);

        // insert link to top or bottom of sub-menu, depending on option
        if (options.addLinkToTop) {
            copiedLi.prependTo($(menu));
        } else {
            copiedLi.appendTo($(menu));
        }
    };

    /**
     * Static method used to collapse sub blocks
     * Is used as part of toggleInner method and on it's own
     * @param event
     * @param options
     * @private
     */
    static _collapseAll (event, options) {
        let $target = $(event.target),
            $item;

        if ($target.hasClass(options.itemClass)) {
            $item = $target;
        } else {
            $item = $target.closest(options.itemClass);
        }

        $item.removeClass('-open');
        $item.find(options.subMenuBlockClass).removeClass('-open');
    };

    /**
     * Static method adding custom options to node
     * @param element
     * @param values
     * @param options
     */
    static customOptions (element, values, options) {
        let str = '';
        values.forEach((property, index) => {
            // if property name is class we add to node it's value
            if (property['option_name'] == 'class') {
                str += ' ' + property['value'];
            } else { // else we treat it like css property and use like it
                if (options.static) {
                    $(element).children(options.itemLabelClass).css(property['option_name'], property['value']);
                } else {
                    $(element).css(property['option_name'], property['value']);
                }
            }
        });
        $(element).addClass(str);
    }

    /**
     * Detect touch device
     * @returns {boolean}
     */
    isTouchDevice () {
        return 'ontouchstart' in window || navigator.msMaxTouchPoints > 0;
    };
};
