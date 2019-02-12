/**
 * Apply PostCSS transformations
 */

'use strict';

var combo = require('./combo'), // eslint-disable-line no-unused-vars
    themes = require('../tools/files-router').get('themes'),
    _ = require('underscore'),

    themeCssOptions = {},
    themePath,

    postcssOptions = {
        options: {
            map: {
                inline: false
            },
            processors: [
                require('autoprefixer')({
                    browsers: ['last 2 Chrome versions', 'last 2 Firefox versions', 'last 2 Safari versions', 'last 2 Edge versions', 'ie 10', 'last 2 iOS versions', 'last 2 ChromeAndroid versions']
                })
            ]
        }
    };

_.each(themes, function (theme, name) {
    themePath = '<%= combo.autopath(\'' + name + '\', path.pub) %>';
    themeCssOptions[name] = {
        files: [
            {
                expand: true,
                cwd: themePath,
                src: ['**/*.css'],
                dest: themePath,
                ext: '.css'
            }
        ]
    };
});

module.exports = _.extend(themeCssOptions, postcssOptions);
