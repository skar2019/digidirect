module.exports = {
    dist: {
        files: {
            '<%= path.web %>/svg/sprite.svg':
                ['<%= path.web %>/svg/min/sprite/**/*.svg']
        }
    },
    options: {
        prefix: 'svgi-',
        includedemo: true,
        includeTitleElement: false
    }
};
