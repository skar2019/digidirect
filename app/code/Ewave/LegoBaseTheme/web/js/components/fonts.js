/* global localStorage, XMLHttpRequest */
// The deferred font loading logic
(function () {
    'use strict';
    // Once cached, the css file is stored on the client forever unless
    var element = document.querySelector('#custom-fonts'),
        fontPath = element.getAttribute('data-theme-path'),
        fontCss = element.getAttribute('data-fonts-css'),
        // Font revision to apply updates
        cssFileRevision = element.getAttribute('data-fonts-revision'),
        // The URL below is changed. Any change will invalidate the cache
        cssHref = fontPath + fontCss;

    // Use the cached revision if we already have it
    if (canWriteToLS() && isFileCached(cssFileRevision)) {
        injectRawStyle(localStorage.font_css_cache);
        // Otherwise, load it with ajax
    } else {
        window.addEventListener('load', function () {
            var xhr = new XMLHttpRequest(),
                text;
            xhr.open('GET', cssHref, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // once we have the content, quickly inject the css rules
                    text = xhr.responseText;
                    text = text.replace(new RegExp('../fonts/', 'g'), fontPath + 'fonts/');
                    injectRawStyle(text);
                    // and cache the text content for further use
                    // notice that this overwrites anything that might have already been previously cached
                    if (canWriteToLS()) {
                        localStorage.font_css_cache = text;
                        localStorage.font_css_revision = cssFileRevision;
                    }
                }
            };
            xhr.send();
        });
    }

    /**
     * Quick way to determine whether a css file has been cached locally
     * @param revision
     * @returns {string|*|boolean}
     */
    function isFileCached (revision) {
        return localStorage.font_css_cache && (localStorage.font_css_revision === revision);
    }

    /**
     * Utility that injects the cached or loaded css text
     * @param text
     */
    function injectRawStyle (text) {
        var style = document.createElement('style');
        style.setAttribute('type', 'text/css');
        style.setAttribute('id', 'inject-font');
        style.innerHTML = text;
        document.getElementsByTagName('head')[0].appendChild(style);
    }

    /**
     * Check support of localStorage (Safari private mode)
     * @returns {boolean}
     */
    function canWriteToLS () {
        try {
            window.localStorage.setItem('_canWriteToLS', 1);
            window.localStorage.removeItem('_canWriteToLS');
            return true;
        } catch (e) {
            return false;
        }
    }
}());
