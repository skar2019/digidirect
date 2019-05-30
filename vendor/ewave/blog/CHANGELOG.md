1.0.0
=============
* Solution Architecture:
    * [#180524](https://ewave.tpondemand.com/entity/180524) -- Blog. Solution Architecture
    
* New features:
    * [#180523](https://ewave.tpondemand.com/entity/180523) -- GENERAL SETTINGS. As an admin, I want to be able to specify the following parameters for the extension
    * [#180526](https://ewave.tpondemand.com/entity/180526) -- MANAGE POSTS. As an admin, I want to be able to manage posts for blog page in backend of Magento
    * [#180527](https://ewave.tpondemand.com/entity/180527) -- MANAGE CATEGORIES. As an admin, I want to be able to manage categories for blog page in backend of Magento
    * [#180528](https://ewave.tpondemand.com/entity/180528) -- MANAGE COMMENTS. As an admin, I want to be able to manage comments for blog page in backend of Magento
    * [#180525](https://ewave.tpondemand.com/entity/180525) -- BLOG POST LIST. As a user, I want to see blog post list page
    * [#180533](https://ewave.tpondemand.com/entity/180533) -- BLOG POST DETAIL. As a user, I want to see blog post detail page
    * [#180534](https://ewave.tpondemand.com/entity/180534) -- CATEGORY LIST. As a user, I want to see category listing page
    * [#192952](https://ewave.tpondemand.com/entity/192952) -- PREVIOUS/NEXT LINK. As a user, I want to be able to see previous/next links on the blog post detail page
    
1.1.0
================
* New features:
    * [#198293](https://ewave.tpondemand.com/entity/198293) -- As a user, I want to see category name for post on blog post listing page    
    
1.2.0
================
* New features:
    * [#192952](https://ewave.tpondemand.com/entity/192952) -- As a user, I want to be able to see previous/next links on the blog post detail page
    * [#199204](https://ewave.tpondemand.com/entity/199204) -- As a user, I want to see hashtags for post on blog post listing page
    
1.2.1
================
* Bugfixes:
    * [#208107](https://ewave.tpondemand.com/entity/208107) -- Qty of posts on page is not matching settings in backoffice; same batch of posts is loaded 6 times if category name is displayed above blog post    
    
1.2.2
================
* Bugfixes:
    * [#215715](https://ewave.tpondemand.com/entity/215715) -- It's impossible to edit any Blog item in the back-office. "Notice: Undefined index"    
    
1.3.0
================
    
* New features:
    * [#190336](https://ewave.tpondemand.com/entity/190336) -- As an admin, I want to be able to create widget with popular blog posts
    * [#190337](https://ewave.tpondemand.com/entity/190337) -- As a user, I want to see widget with popular blog posts on the frontend
    * [#207996](https://ewave.tpondemand.com/entity/207996) -- As a user, I want to be able to see category name for a blog post    
    
1.3.1
================
* Bugfixes:
    * [#217672](https://ewave.tpondemand.com/entity/217672) -- Category View page is available for disabled category on storefront
    * [#217673](https://ewave.tpondemand.com/entity/217673) -- It is impossible to reach Category page on storefront Category Url Prefix = empty    
    
1.3.2
================
* Bugfixes:
    * [#229026](https://ewave.tpondemand.com/entity/229026) --  Exception error on blog category page    
    
1.3.3
==================
* Bugfixes:
    * [#254402](https://ewave.tpondemand.com/entity/254402) -- 'Write a comment' field is not located within the form    

1.3.4
==================
* Improvements:
    * Ability to get any type of images by key for the Blog Post
    
1.3.5
===================
* Bugfixes:
    * [#260204](https://ewave.tpondemand.com/entity/260204) -- Creating (editing, deleting?) blog post does not trigger FPC invalidation    
    
1.3.6
===================
* Bugfixes:
    * [#257747](https://ewave.tpondemand.com/entity/257747) -- [Blog] Link in footer to be consistent with other links

1.3.7
===================
* Bugfixes:
    * [#265688](https://ewave.tpondemand.com/entity/265688) -- Please translate related Chinese contents
    * [#267440](https://ewave.tpondemand.com/entity/267440) -- Incorrect 'Publish Date' is shown on FE

1.3.8
===================
* Bugfixes:
    * [#268878](https://ewave.tpondemand.com/entity/268878) -- Related posts and products are not saved for Blog post

2.0.1
===================
* Bugfixes:
    * [#284501](https://ewave.tpondemand.com/entity/284501) -- Mismatch between dates on front and backend for blog posts
    
2.1.0
===================
* New features:
    * [#289240](https://ewave.tpondemand.com/entity/289240) -- As an admin, I want to be able to configure Blog pages output for XML sitemap

* Bugfixes:
    * [#290042](https://ewave.tpondemand.com/entity/290042) -- Generated url for posts and categories is incorrect    
    
2.2.0
====================
* New features:
    * [#190342](https://ewave.tpondemand.com/entity/190342) -- [BLOG CATEGORIES WIDGET BACKEND] As an admin, I want to be able to create widget with popular blog posts
    * [#289888](https://ewave.tpondemand.com/entity/289888) -- [BLOG CATEGORIES WIDGET FRONTEND] As a user, I want to see widget with blog categories on the frontend

* Bugfixes:
    * [#290347](https://ewave.tpondemand.com/entity/290347) -- [BLOG CATEGORIES WIDGET FRONTEND] Child category is not displayed in case parent doesn't have any posts
    * [#290402](https://ewave.tpondemand.com/entity/290402) -- [BLOG CATEGORIES WIDGET FRONTEND] Blog Category is duplicated in case only child categories are selected
    * [#290476](https://ewave.tpondemand.com/entity/290476) -- [BLOG CATEGORIES WIDGET FRONTEND] Parent category is not displayed in case first child has not posts
	
2.2.1
====================
* Bugfixes:
    * [#290411](https://ewave.tpondemand.com/entity/290411) -- [Blog] Counter is calculated posts which have Publish Date > current
    * [#290425](https://ewave.tpondemand.com/entity/290425) -- [BLOG CATEGORIES WIDGET FRONTEND] Sorting for child category doesn't work
    * [#291644](https://ewave.tpondemand.com/entity/291644) -- [BLOG CATEGORIES WIDGET FRONTEND] HTTP ERROR 500 in case open blog post from blog category widget
    * [#292508](https://ewave.tpondemand.com/entity/292508) -- [PROJECT: CONVERSE] ewave/blog error

2.2.2
====================
* Bugfixes:
    * [#292427](https://ewave.tpondemand.com/entity/292427) -- [LEGO][Blog][Blog Post Listing] Tag of the currently opened tag listing page is not highlighted

2.3.0
===================
* New features:
    * [#293001](https://tp.ewave.com/293001) -- [BLOG TRANSLATION] As an admin, I want Blog settings to be updgraded
	
2.3.1
===================
* Bugfixes:
    * [#292586](https://ewave.tpondemand.com/entity/292586) -- [Project: Rip Curl][Back-office][Ewave Blog] Publish date of the blog posts in the grid 'Manage Blog Posts' doesn't match the publish date of the post items	

2.4.0
===================
* New features:
    * [#294888](https://tp.ewave.com/294888) -- [CONTEXTUAL 301 REDIRECTS] As an admin, I want contextual 301 redirects configuration to be available in Blog module
	
2.4.1
===================
* Bugfixes:
    * [#294253](https://ewave.tpondemand.com/entity/294253) -- [Project: DigiDirect] Ewave_Blog module doesn't have verification for Breadcrumps module
    * [#297756](https://ewave.tpondemand.com/entity/297756) -- [Project: Rip Curl][Translate Blog][Back-office] Categories/Posts created only for a certain store view are not displayed in the corresponding grids in the back-office

2.4.2
* Bugfixes:
    * [#294253](https://ewave.tpondemand.com/entity/294253) -- [Project: DigiDirect] Ewave_Blog module doesn't have verification for Breadcrumps module	
	
2.4.3
====================
* Bugfixes:
    * [#294253](https://ewave.tpondemand.com/entity/294253) -- [Project: DigiDirect] Ewave_Blog module doesn't have verification for Breadcrumps module
    * [#302054](https://ewave.tpondemand.com/entity/302054) -- [Project; DigiDirect] Trying to add config product from the "Related products" section redirects user to the 404 page	
	
2.5.0
====================	
New features:
    * [#302053](https://ewave.tpondemand.com/entity/302053) -- As an admin, I want to be able to setup a theme for blog pages

* Bugfixes:
    * [#306068](https://ewave.tpondemand.com/entity/306068) -- [Blog] It's impossible to open blog post/category page if custom theme is set for blog	

2.5.1	
====================
* Bugfixes:
    * [#302248](https://ewave.tpondemand.com/entity/302248) -- [Project: DigiDirect] Sort order could not be changed for the assigned post in the widget
    * [#306536](https://ewave.tpondemand.com/entity/306536) -- [Blog] An empty value is not added to Theme dropdown	
