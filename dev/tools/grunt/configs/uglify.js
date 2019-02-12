module.exports = {
    options: {
        preserveComments: /^!/,
        sourceMap: false
    },
    inline: {
        files: [
            {
                expand: true,
                cwd: 'vendor/ewave/legobasetheme/web/js/components/',
                src: ['svg-sprite.js'],
                dest: '<%= path.web %>/js/components/',
                rename: function (dst, src) {
                    return dst + '/' + src.replace('.js', '.min.js');
                }
            }
        ]
    }
};
