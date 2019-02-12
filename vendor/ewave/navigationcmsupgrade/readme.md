###Description
The extension allows to generate script to migrate "Ewave_Navigation" menu data from local machine to remote and vice versa

Extension uses Ewave_CmsUpgrade extension functionality to generate upgrade script and depends on Ewave_Navigation module

For apply upgrade script please use console command: 
```php
bin/magento ewave:cms_upgrade
```

For apply a single upgrade script please use console command with version, example:
```php
bin/magento ewave:cms_upgrade 0.0.1-0.0.2
```

### VERSION 1.0.1

   1. Fixed bugs related to Ewave_CmsUpgrade extension updates

### VERSION 1.0.2

   1. [M2.2] There is an error during the reindex process
