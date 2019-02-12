1.0.0
=============
* Solution Architecture:
    * [#91282](https://ewave.tpondemand.com/entity/91282) -- CMS Upgrade. Solution architecture
* New features:
    * [#91300](https://ewave.tpondemand.com/entity/91300) -- CMS PAGES. As an admin, I want to be able to create upgrade script for cms pages
    * [#91301](https://ewave.tpondemand.com/entity/91301) -- STATIC BLOCKS. As a n admin, I want to be able to create upgrade script for static blocks
    * [#91302](https://ewave.tpondemand.com/entity/91302) -- CONFIGURATION SETTINGS. As an admin, I want to be able to create upgrade script for system configuration
    * [#122233](https://ewave.tpondemand.com/entity/122233) -- SCRIPT FORMATTING. As a system, I want created scripts to be formatted

1.0.1
=============
* Bugfixes:
    * [#164263](https://ewave.tpondemand.com/entity/164263) -- Magento system configuration settings are not shown at all when disable the module 'Ewave_CmsUpgrade' in Advanced section

1.1.0
=============
* New features:
    * [#162599](https://ewave.tpondemand.com/entity/162599) -- UPRGADE SCRIPT NAME. As a system, I want to be able to install sripts in spite of the order of the scripts in release folder

1.1.1
=============
* Bugfixes:
    * [#166666](https://ewave.tpondemand.com/entity/166666) -- Upgrade script for Configuration failed with an error
    
1.2.0
=============
* New features:
    *[#144228](https://ewave.tpondemand.com/entity/144228) -- TRANSACTION EMAIL. As a user, I want to be able to generate upgrade script for transaction email templates
    *[#144227](https://ewave.tpondemand.com/entity/144227) -- WIDGET. As an admin, I want to be able to generate upgrade script for widgets
    *[#165877](https://ewave.tpondemand.com/entity/165877) -- ADVANCED SETTINGS. As an admin, I want to be able to generate upgrade script for advanced settings
    *[#160596](https://ewave.tpondemand.com/entity/160596) -- BANNER. As a user, I want to be able to generate upgrade script for banner templates
    *[#167527](https://ewave.tpondemand.com/entity/167527) -- CMS PAGES/BLOCKS. As an admin, I want to generate script for cms pages/blocks with page/background images
    *[#175895](https://ewave.tpondemand.com/entity/175895) -- CMS MOBILE. As an admin, I want to be able to generate script for cms pages with mobile content
* Bug fixes:
    *[#176819](https://ewave.tpondemand.com/entity/176819) -- Upgrade sсript: CMS page. Meta Title is not saved with upgrade script
    
1.2.1
=============
* Bug fixes:
    *[#178096](https://ewave.tpondemand.com/entity/178096) -- Upgrade script checkbox is not removed if extension is disabled

1.2.2
=============
* Bug fixes:
    *[#169176](https://ewave.tpondemand.com/entity/169176) -- CMS-upgrade: Fatal error
    
1.2.3
=============
* Bugfixes:
    * [#202836](https://ewave.tpondemand.com/entity/202836) -- Error on script execution    

1.2.4
=============
* Bugfixes:
    * [#205089](https://ewave.tpondemand.com/entity/205089) -- [PROJECT: Neverfail] Stores->Configuration setting are not updated with CMS Upgrade script
    
1.2.5
=============
* Improvements:
    * theme_id improvements for widgets. Now theme code is used while import/export widgets
    * Now "adminhtml" is emulated. Fixed "catalog" import setting
    
1.2.6
==============
* Bugfixes:
    * Fixed bug with theme_id for old upgrade scripts
* Improvements:
    * Insert last updated version into database table if version was not presented in database
    * Banner attributes are updated in database if images serialization are failed 
