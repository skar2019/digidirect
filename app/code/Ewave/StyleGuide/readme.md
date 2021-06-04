# Ewave StyleGuide

TODO: 
1. https://ewave.tpondemand.com/entity/278394-as-a-user-i-want-to
2. https://ewave.tpondemand.com/entity/278416-as-an-extension-i-want-to

Go to back office: **Stores - Configuration - Advanced - Developer - StyleGuide** and choose how to show the StyleGuide. 
If you choose 'enable', StyleGuide will be displayed in context of every page and on a separate page. 
The url of a separate page is 'styleguide'.

Some default components are already added, but you can always add new or disable useless ones. 
In order to do this, some manipulations in 'default.xml' layout are needed. 
Please see below how to do it manually or automatically.

## How to do it automatically

Create new task for Grunt:

Create file **dev/tools/grunt/tasks/styleguide.js** with the following content:

```
module.exports = function (grunt) {
    grunt.registerMultiTask('styleguide', function () {
        let pathToTemplates, pathToLayout, exclude;
        if (this.data.options && this.data.options.themeName) {
            pathToTemplates = `./app/design/frontend/${this.data.options.themeName}/Ewave_StyleGuide/web/template/*`;
            pathToLayout = `app/design/frontend/${this.data.options.themeName}/Ewave_StyleGuide/layout/default.xml`;
        } else {
            pathToTemplates = `./vendor/ewave/styleguide/view/frontend/web/template/*`;
            pathToLayout = `./vendor/ewave/styleguide/view/frontend/layout/styleguide_components.xml`
        }

        if (this.data.options && this.data.options.exclude) {
            exclude = this.data.options.exclude;
        } else {
            exclude = [];
        }
        const templates = grunt.file.expand(pathToTemplates);
        const xml = `<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <referenceBlock name="styleguide">
        <arguments>
            <argument name="jsLayout" xsi:type="array">
            <item name="components" xsi:type="array">
                <item name="styleguide" xsi:type="array">
                    ${this.target === 'developer' ? `<item name="component" xsi:type="string">Ewave_StyleGuide/js/styleguide</item>` : ``}
                    <item name="config" xsi:type="array" />
                    <item name="children" xsi:type="array">
                            ${templates.map(template => {
                                const componentName = template.split('/').pop().replace('.html', '');
                                
                                return `<item name="${componentName}" xsi:type="array">
                                    <item name="component" xsi:type="string">Ewave_StyleGuide/js/styleguide</item>
                                    <item name="config" xsi:type="array">
                                        <item name="title" xsi:type="string">${componentName.replace(/-/g, ' ')}</item>
                                        <item name="template" xsi:type="string">Ewave_StyleGuide/${componentName}</item>
                                    </item>
                                </item>`;
                            }).join('\n\n')}

                            ${exclude.map(el => `<item name="${el}" xsi:type="array">
                                <item name="config" xsi:type="array">
                                    <item name="componentDisabled" xsi:type="boolean">true</item>
                                </item>
                            </item>`).join('\n\n')}
                        </item>
                    </item>
                </item>
            </argument>
        </arguments>
    </referenceBlock>
</page>
`;

        grunt.file.write(pathToLayout, xml);
    });
};
```
Create configuration for this task:

Create file **dev/tools/grunt/configs/styleguide.js** with the following content

```
const themes = require('../tools/files-router').get('themes'),
           _ = require('underscore');

const styleguideOptions = {};

_.each(themes, function(theme, name) {
    styleguideOptions[name] = {
        options: {
            themeName: theme.name,
            exclude: [] // components that you want to exclude
        }
    };
});

styleguideOptions.developer = {};

module.exports = styleguideOptions;
```

Now just create new components in **Ewave_StyleGuide/web/template** in your theme and run `grunt styleguide:yourtheme`. 
Grunt will create a layout for you.

You may also want to override some of the default templates. 
In this case you should tell Grunt about it and paste the names of these components in the config: 

```
_.each(themes, function(theme, name) {
    styleguideOptions[name] = {
        options: {
            themeName: theme.name,
            exclude: ['headings', 'messages'] // components that you want to exclude
        }
    };
});
```

If you want to add new components to this module, add new template and run `grunt styleguide:developer`.
Grunt will update the layout in `vendor/ewave/styleguide/view/frontend/layout/components.xml`.

## How to do it manually
 
Create a file **Ewave_StyleGuide/layout/default.xml** in your theme with the following content:

```
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <referenceBlock name="styleguide">
        <arguments>
            <argument name="jsLayout" xsi:type="array">
                <item name="components" xsi:type="array">
                    <item name="styleguide" xsi:type="array">
                        <item name="config" xsi:type="array" />
                        <item name="children" xsi:type="array">
                            <item name="headings" xsi:type="array">
                                <item name="component" xsi:type="string">Ewave_StyleGuide/js/styleguide</item>
                                <item name="config" xsi:type="array">
                                    <item name="title" xsi:type="string">Headings</item>
                                    <item name="template" xsi:type="string">Ewave_StyleGuide/headings</item>
                                </item>
                            </item>

                            <item name="messages" xsi:type="array">
                                <item name="component" xsi:type="string">Ewave_StyleGuide/js/styleguide</item>
                                <item name="config" xsi:type="array">
                                    <item name="title" xsi:type="string">Messages</item>
                                    <item name="template" xsi:type="string">Ewave_StyleGuide/messages</item>
                                </item>
                            </item>
                        </item>
                    </item>
                </item>
            </argument>
        </arguments>
    </referenceBlock>
</page>
```

In this file you should define component names and paths to their templates. 
Templates should be stored in **Ewave_StyleGuide/web/template** folder in your theme.

You can also disable some of the default components that you don't want to be displayed. 
To do this go to **Ewave_StyleGuide/layout/default.xml** in your theme and paste this code next to other components:

```
<item name="breadcrumbs" xsi:type="array">
    <item name="config" xsi:type="array">
        <item name="disabled" xsi:type="string">true</item>
    </item>
</item>
```

The above code is used to disable breadcrumbs.