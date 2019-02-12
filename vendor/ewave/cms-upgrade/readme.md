### Description

All upgrade script will be saved to app/code/Ewave/cmsupgradedata folder.

For apply upgrade script please use console command: 
```php
bin/magento ewave:cms_upgrade
```

For apply a single upgrade script please use console command with version, example:
```php
bin/magento ewave:cms_upgrade 0.0.1-0.0.2
```

### VERSION 1.0.0

  1. Bugfix #144071. Error on attempt to run Upgrade script for configuration settings

### VERSION 1.0.1

  1. Bugfix #164263. Magento system configuration settings are not shown at all when disable the module 'Ewave_CmsUpgrade' in Advanced section

### VERSION 1.1.0

  1. US #162599. As a system, I want to be able to install scripts in spite of the order of the scripts in release folder

### VERSION 1.1.1

  1. Bugfix #166666. Upgrade script for Configuration failed with an error
  
### VERSION 1.2.0

  1. added ability to generate upgrade script for widgets
  2. added ability to generate upgrade script for transaction email templates
  3. added ability to generate upgrade script for banners
  4. added ability to generate upgrade script for "Advanced" tab in "Stores/Configuration"
  5. fixed missed "meta_title" field
  
### VERSION 1.2.3  

  1. Fixed area code bug

### VERSION 1.2.4

  1. Fixed Vice Versa error.

### VERSION 1.2.5
  1. Improved widgets import/export. Widget theme is set by code
  2. Adminhtml area is emulated with magento method
  
### VERSION 1.2.6
  1. If last version is not presented in database now it will be inserted
  2. Fixed problem with old widgets where theme_id was integer
  3. IF images serialization/deserialization failed banner will be updated without "images" attribute
  
### VERSION 1.2.7
  1. Fix directory on installation
  