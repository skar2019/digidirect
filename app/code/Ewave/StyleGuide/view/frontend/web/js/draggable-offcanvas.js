/* global Image, requestAnimationFrame */

define(function () {
    'use strict';

    function applier (func) {
        var args = Array.prototype.slice.call(arguments, 1);

        return function (item) {
            return func.apply(item, args);
        };
    }

    return function (config, offcanvas) {
        var toggleElement = offcanvas.querySelectorAll(config.toggleSelector || '[data-action=toggle]'),
            resizer = offcanvas.querySelectorAll(config.resizerSelector || '[data-action=resize]'),
            styleguide = offcanvas.querySelector(config.styleguide || '[data-block=styleguide]'),
            resizerPosition = config.initialMaxHeight || 300,
            dragPreview = new Image();

        toggleElement.forEach(applier(window.addEventListener, 'click', toggleStyleguide));
        // TODO: use applier
        toggleElement.forEach(function (toggle) { toggle.classList.add('-initialized'); });

        resizer.forEach(applier(window.addEventListener, 'dragstart', dragstart));
        resizer.forEach(applier(window.addEventListener, 'drag', drag));

        function toggleStyleguide (e) {
            offcanvas.classList.toggle(config.activeClass || '-active');
        }

        function dragstart (e) {
            resizerPosition = e.screenY;
            e.dataTransfer.setDragImage(dragPreview, 0, 0);
        }

        function drag (e) {
            var delta = e.screenY - resizerPosition;

            resizerPosition = e.screenY;
            requestAnimationFrame(applier(resize, delta));
        }

        function resize (delta) {
            var prevHeight = styleguide.clientHeight;

            styleguide.style.maxHeight = prevHeight + delta + 'px';
        }
    };
});
