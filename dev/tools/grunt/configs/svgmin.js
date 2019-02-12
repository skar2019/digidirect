module.exports = {
    dist: {
        files: [{
            expand: true,
            cwd: '<%= path.web %>/svg/src/',
            src: '**/*.svg',
            dest: '<%= path.web %>/svg/min/'
        }]
    },
    options: {
        plugins: [
            { sortAttrs: true },
            { removeTitle: true },
            { removeDimensions: true },
            { removeViewBox: false },
            { removeEmptyAttrs: false }
        ]
    }
};
