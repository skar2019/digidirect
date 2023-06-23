var config = {
    'paths': {
        'dmpt': 'Sparsh_AbandonedCart/js/dmpt',
        'stick-to-me' : 'Sparsh_AbandonedCart/js/stick-to-me'
    },
    'shim': {
        'dmpt': {
            exports: '_dmTrack',
            deps: ['jquery']
        },
        'stick-to-me': {
            deps: ['jquery']
        }
    }
};
