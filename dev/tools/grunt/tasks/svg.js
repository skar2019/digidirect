'use strict';

module.exports = function (grunt) {
    grunt.registerTask(
        'svg',
        'SVG sprite processing: svgmin + svgstore + svgtobase64',
        ['clean:svg', 'svgmin', 'svgstore', 'svgtobase64', 'uglify:inline', 'writefile:inlinesvg']
    );
};
