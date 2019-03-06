define([
    'jquery',
    'matchMedia',
    'domReady!',
    'customScrollbarInit'
], function ($, mediaCheck) {
    'use strict';

    var action = document.querySelectorAll('.tab-in'),
        maxHeight = 715;

    Array.prototype.forEach.call(action, function (el) {
        var control = el.getAttribute('data-alias'),
            tab = document.getElementById(control),
            button = tab.querySelector('.read-more');
        checkElement(tab);

        el.addEventListener('click', function () {
            var control = el.getAttribute('data-alias'),
                tab = document.getElementById(control);
            checkElement(tab);
        });

        button.addEventListener('click', toggleBar);

        function toggleBar() {
            if (tab.classList.contains('-closed')) {
                tab.classList.remove('-closed');
            } else {
                tab.classList.add('-closed');
            }
        }

        function checkElement(tab) {
            var height = tab.offsetHeight;

            if (height > maxHeight) {
                tab.classList.add('more');
            }
        }
    });

    mediaCheck({
        media: '(min-width: 1024px)',
        entry: function () {
            $('.right-bar').customScrollbar();
        },
        exit: function () {
            $('.right-bar').customScrollbar('destroy');
        }
    });
});