define(['jquery'], function ($) {
    'use strict';
    return function (originalAutocomplete) {
        return function (options, algoliaBundle) {
            const instance = originalAutocomplete(options, algoliaBundle);

            console.log('[Algolia] Wrapper instance captured');

            // Wrap ALL possible methods
            Object.keys(instance).forEach(function(key) {
                if (typeof instance[key] === 'function') {
                    const originalMethod = instance[key].bind(instance);
                    instance[key] = function() {
                        console.log('[Algolia] Method called: ' + key);
                        const result = originalMethod.apply(this, arguments);

                        // After any method, check if autocomplete was created
                        if (instance.autocomplete || instance.autocompleteInstance) {
                            window.algoliaAutocompleteInstance = instance.autocomplete || instance.autocompleteInstance;
                            console.log('[Algolia] Autocomplete found after ' + key + '! ✅', window.algoliaAutocompleteInstance);
                        }

                        return result;
                    };
                }
            });

            // Also store the wrapper itself - we might need it
            window.algoliaWrapperInstance = instance;
            console.log('[Algolia] Wrapper stored globally');

            return instance;
        };
    };
});
