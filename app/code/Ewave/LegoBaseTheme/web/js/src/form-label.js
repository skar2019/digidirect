import $ from 'Magento_Ui/js/lib/view/utils/async';

export default class FormLabel {
    constructor (options = {}) {
        this.options = Object.assign({}, {
            selector: '.field:not(.choice):not(.-custom)',
            formElements: 'input, select, textarea',
            activeClass: '-active'
        }, options);
        this.pool = {
            [this.options.selector]: new Map()
        };

        $.async(this.options.formElements, (node) => {
            $(node).filter(this.hasClosest(this.options)).each(this.initLabel(this.options));
        });
    }

    hasClosest (options) {
        return (i, el) => this.getClosestElement(el, options.selector);
    }

    getClosestElement (el, selector) {
        let ctxPool = this.pool[selector],
            closestElement;

        if (ctxPool.has(el)) {
            closestElement = ctxPool.get(el);
        } else {
            closestElement = $(el).closest(selector)[0];
            ctxPool.set(el, closestElement);
        }

        return closestElement;
    }

    initLabel (options) {
        return (i, el) => {
            let ctxPool = this.pool[options.selector],
                fieldElement = ctxPool.get(el);

            if (el.value.trim() !== '' || this.isAutoFill($(el))) {
                fieldElement.classList.add(options.activeClass);
            }

            $(el).on('focus change', () => {
                fieldElement.classList.add(options.activeClass);
            }).on('blur', (e) => {
                if (e.target.value.trim() === '') {
                    fieldElement.classList.remove(options.activeClass);
                }
            });

            ctxPool.delete(el);
        };
    }

    isAutoFill ($el) {
        try {
            return $el.is(':-webkit-autofill');
        } catch (e) {
            return false;
        }
    }
}

new FormLabel(module.config());
