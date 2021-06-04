define([
    'uiComponent',
    'mage/translate',
    'Ewave_StyleGuide/js/highlight.pack'
], function (Component, $t, hljs) {
    'use strict';

    return Component.extend({
        options: {
            codeSelector: '[data-styleguide="code-example"]'
        },

        highlight: function (nodes) {
            if (nodes && nodes.length) {
                var codeWrapper = nodes[0].parentNode;
                codeWrapper.textContent = codeWrapper.innerHTML;
                hljs.highlightBlock(codeWrapper);
            }
        },

        viewSource: function (data, event) {
            var button = event.target,
                viewer = button.parentNode;

            if (viewer.classList.contains('-closed')) {
                viewer.classList.remove('-closed');
                button.textContent = $t('Hide source');
            } else {
                viewer.classList.add('-closed');
                button.textContent = $t('View source');
            }
        },

        copyCode: function (data, event) {
            var button = event.target,
                code = button.parentNode.querySelector(this.options.codeSelector),
                range,
                selection,
                textOnButton;

            if (document.body.createTextRange) {
                range = document.body.createTextRange();
                range.moveToElementText(this.nextSibling);
                range.select();
            } else if (window.getSelection) {
                selection = window.getSelection();
                range = document.createRange();
                range.selectNodeContents(code);
                selection.removeAllRanges();
                selection.addRange(range);
            }

            document.execCommand('copy');
            textOnButton = button.textContent;
            button.textContent = $t('Copied');

            setTimeout(function () {
                button.textContent = textOnButton;
            }, 1000);
        }
    });
});
