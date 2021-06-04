1.0.0
=============
* Solution Architecture:
    * [#149695](https://ewave.tpondemand.com/entity/149695) -- Banner Rotator. Solution Architecture
* New features:
    * [#147678](https://ewave.tpondemand.com/entity/147678) -- GENERAL SETTING. As an admin, I want to be able to specify image / MIME types for the banners 
    * [#149593](https://ewave.tpondemand.com/entity/149593) -- WIDGET SETTINGS. As an admin, I want to be able to specify rotation frequency and enable bullets / loop for the banner
    * [#144251](https://ewave.tpondemand.com/entity/144251) -- MANAGE BANNER. As an admin, I want to be able to specify product for the banner easily 
    * [#149605](https://ewave.tpondemand.com/entity/149605) -- CACHE. As an admin, I want to be able to flash cache of the banners images when I flash "Flush Catalog Images Cache
    
1.1.0
=============
* New features:
    * [#165667](https://ewave.tpondemand.com/entity/165667) -- FRONTEND VIDEO POPUP. As a user, I want to be able to see video pop-up for the banner if it is enabled
    * [#165145](https://ewave.tpondemand.com/entity/165145) -- VIDEO ROLES EXCEPTIONS. As an admin, I want to be able to specify a role for a video to be used
    * [#165143](https://ewave.tpondemand.com/entity/165143) -- PLAY AUTOMATICALLY. As an admin, I want to be able to play video automatically on desktop OR mobile
    * [#165141](https://ewave.tpondemand.com/entity/165141) -- VIDEO POPUP. As an admin, I want to be able to enable video pop-up for the video on the banner
    * [#154340](https://ewave.tpondemand.com/entity/154340) -- CHANGE IMAGE ROLE. As an admin, I want to be able to change image role for the image (see description)
    * [#157571](https://ewave.tpondemand.com/entity/157571) -- FRONTEND VIDEO. As a user, I want to see video on the banner rotator on the page (see description)
    * [#155497](https://ewave.tpondemand.com/entity/155497) -- VIDEO. As an admin, I want to be able to add video to banner (see description)

1.1.1
=============
* Bug fixes:
    * [#177368](https://ewave.tpondemand.com/entity/177368) -- Banner. There is an error in console if perform compilation

1.1.2
==============
* Bugfixes:
    * [#173117](https://ewave.tpondemand.com/entity/173117) -- Banner - Video. IE11. "Play" button is not hidden when video is played

1.2.0
==============
* New features:
    * [#191256](https://ewave.tpondemand.com/entity/191256) -- As a system I should have the ability for HTML5 Video player controls to be styled

* Bugfixes:
    * [#172678](https://ewave.tpondemand.com/entity/172678) -- IE11. Video in banner is not played from the beginning
    * [#192899](https://ewave.tpondemand.com/entity/192899) -- IE11. Video is not played automatically in the popup
    
1.3.0
===============    
* New features:
    * [#198835](https://ewave.tpondemand.com/entity/198835) -- As a system, I want data test attributes to be added to html templates for automation testing

1.3.1
===============
* Bugfixes:
    * [#200049](https://ewave.tpondemand.com/entity/200049) -- WYSIWYG settings are not applied for banner 
       
1.3.2
===============
* Bugfixes:
    * [#206234](https://ewave.tpondemand.com/entity/206234) -- BO: Banners: "Custom Link" setting of the banner in Banner rotator widget is cleared after the Flash Catalog Image Cache process
           
2.0.0
================
* Bugfixes:
    * [#221362](https://ewave.tpondemand.com/entity/221362) -- Banner Rotator: Home page banner is blocked if ad block extension is installed under certain conditions. 
               IMPORTANT! After updating images will be uploaded to new folder and you will need to rename "banners" folder
    
2.0.1
================
 * Bugfixes:
     * [#228364](https://ewave.tpondemand.com/entity/228364) -- Impossible to Flush Catalog Images Cache: "An error occurred while clearing the image cache."   
     
2.0.2
================
* Bugfixes:
    * [#231362](https://ewave.tpondemand.com/entity/231362) -- Performance: improve Ewave_Banner performance     
    
2.0.3
=================
* Bugfixes:
    * [#204951](https://ewave.tpondemand.com/entity/204951) -- BO: Banner: The Customer Segments field is shifted on the Banner Details page    

2.0.4
=================    
* Bugfixes:
    * [#241310](https://ewave.tpondemand.com/entity/241310) -- 'Customer Segments' setting doesn't work for banners    
    
2.0.5
=================
* Bugfixes:
    * [#252080](https://ewave.tpondemand.com/entity/252080) -- Guest segment banner disappears after browsing the website

2.0.6
=================
* Improvements:
    * Renamed Magento_Adminhtml resource to Magento_Backend as it's done in Magento Core
    
2.0.7
==================
 * Bugfixes:
     * [#265513](https://ewave.tpondemand.com/entity/265513) -- Text banner text content not displayed specific language content   
   
2.0.8
==================
* Bugfixes:
    * [#267222](https://ewave.tpondemand.com/entity/267222) -- EE VERSION Can't override less variables
    
2.1.0
======================
* New features:
    * [#270614](https://ewave.tpondemand.com/entity/270614) -- As an admin, I want Video Title to be a non-mandatory field
    
3.0.0
======================
* New features:
    * [#267197](https://ewave.tpondemand.com/entity/267197) -- As a customer, I want Banner Rotator extension's performance to be optimized  

3.0.2
=====================
* Bugfixes:
    * [#281574](https://ewave.tpondemand.com/entity/281574) -- Last banner is displayed on FE even if another banner is assigned to the widget - Incorrect rewriting of variable
    	
3.0.3
=====================
* Bugfixes:
    * [#282375](https://ewave.tpondemand.com/entity/282375) -- Impossible to open page with new created widget - 503 error - "Warning: simplexml_load_string(): Entity: line 1: parser error : Entity 'larr' not defined

4.0.0
* New features:
    * [#287253](https://ewave.tpondemand.com/entity/287253) -- [2.3] As an extension, I want to be compatible with Magento 2.3	

4.0.1
* Bugfixes:
    * [#297330](https://ewave.tpondemand.com/entity/297330) -- [Magento 2.3.1] 500 Internal Server Error in case adding Dynamic Blocks
