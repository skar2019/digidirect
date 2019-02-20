import $ from 'jquery';
import {loadAddOn} from 'ewaveUtils';
import 'jquery/ui';
import 'domReady!';

$.widget('ewave.truncateDotdotdot', {
    version: '0.0.1',
    options: {
        dotdotdot: '...',
        step: 3,
        dataAttr: 'data-truncate-default-text'
    },
    _create() {
        this.$element = $(this.element);
        setTimeout(() => {
            this.truncate();
        }, 0);
    },
    _loadAddOn() {
        let options = this.options,
            store = this.store,
            events = this.events;
        loadAddOn({options, store, events}, this, 'Truncate Dotdotdot');
        return this;
    },
    _setOptions(key, value) {
        this._super("_setOption", key, value);
    },
    destroy() {
        $.Widget.prototype.destroy.call(this);
    },
    truncate() {
        let $element = this.$element,
            $parent = $element.parent(),
            $clone = $element.clone(),
            defaultText = $element.attr(this.options.dataAttr) || $element.text(),
            text = $element.text(),
            height = $element.height(),
            parentHeight = $parent.height();

        if (height <= parentHeight) {
            return;
        }
        $clone.css({
            visibility: 'hidden',
            position: 'absolute',
            width: $parent.width() + 'px',
        });
        $element.after($clone);

        let length = text.length - this.options.step;
        for (; length >= 0 && $clone.height() > parentHeight; length -= this.options.step) {
            $clone.text(text.substring(0, length) + this.options.dotdotdot);
        }

        $element.text($clone.text()).attr(this.options.dataAttr, defaultText);
        $clone.remove();
    }
});

export default $.ewave.truncateDotdotdot;