module.exports = function (grunt) {
    'use strict';

    var fs = require('fs'),
        path = require('path');

    grunt.registerTask('svgtobase64', 'SVG files to base64 variables', function () {
        var options = this.options({
            iconsPath: 'vendor/ewave/defaulttheme/web/base64/',
            outputFile: 'vendor/ewave/defaulttheme/web/css/source/lib/ewave/variables/_base64.less',
            variablePrefix: '@base64-icon__'
        });

        var stringBase64 = '',
            data = fs.readdirSync(options.iconsPath), // all files in folder
            stats,
            item,
            content,
            variableName,
            variableValue;

        for (item in data) {
            checkFile(options.iconsPath, data[item]);
        }

        function checkFile(path, item) {
            var fullPath = path + item,
                data,
                fl;
            try {
                // Query the entry
                stats = fs.lstatSync(fullPath);
                // Is it a directory?
                if (stats.isDirectory()) {
                    data = fs.readdirSync(fullPath); // all files in folder
                    for (fl in data) {
                        checkFile(fullPath + '/' , data[fl]);
                    }
                } else {
                    convertToBase64(fullPath, item);
                }
            } catch (e) {
                console.log(e);
            }
        }

        function convertToBase64(file, name) {
            content = fs.readFileSync(file); // get file content
            variableName = options.variablePrefix + path.basename(name, '.svg'); // collect the variable name
            variableValue = ': url(data:image/svg+xml;base64,' + content.toString('base64') + ')'; // variable value
            stringBase64 = stringBase64 + variableName + variableValue + ';\n'; // concat all variables to one string
        }

        grunt.file.write(options.outputFile, stringBase64); // write string to file
    });
};
