'use strict';

module.exports = {
    options: {
        sourceMap: false,
        babelrc: false,
        presets: [
            ['env', {
                'modules': 'amd',
                'targets': {
                    'browsers': ['ios >= 8', 'android >= 4', 'last 2 versions', 'not ie <= 11']
                }
            }],
            'stage-1'
        ],
        plugins: ['add-module-exports']
    },
    dist: {
        files: [{
            expand: true,
            cwd: 'app/design/frontend/',
            src: ['**/web/js/src/**/*.js'],
            dest: 'app/design/frontend/',
            ext: '.js',
            rename: function (dest, src) {
                return dest + src.replace('/src/', '/dist/');
            }
        }]
    }
};
