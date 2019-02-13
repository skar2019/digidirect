define([
    'jquery',
    'domReady!',
    'customScrollbarInit'
], function ($) {
    'use strict';

    var action = document.querySelectorAll('.readmore'),
        container = document.querySelector('.content.more');

    $('.right-bar').customScrollbar();

    [].forEach.call(action, function (el) {
        el.addEventListener('click', toggleBar);
    });

    function toggleBar() {
        if (container.classList.contains('-closed')) {
            container.classList.remove('-closed');
        } else {
            container.classList.add('-closed');
        }
    }
});
