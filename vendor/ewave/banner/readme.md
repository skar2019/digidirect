Banner Rotator
==============

[wiki link](https://wiki.ewave.com/display/LEGO/Banner+Rotator)

### Description
The extension allows to upload banner images via custom uploader and configure sizes/roles for images to use "srcset" html5 functionality


1) allow upload images and specify roles
2) allow specify link form image: category, custom link, product
3) allow specify navigation type on "Banner Rotator" widget level: bullets, navigation titles and without

##### An admin is able to:

Create/Edit banner. User is able to:
- upload and specify roles for images. Image can be saved without role;
- specify navigation type for banner: navigation titles(wysiwyg field), bullets and without navigation
- specify link for image: category, product or custom link

##### Developers info: 
To add new image type, for example "desktop",  use view.xml

    <vars module="Ewave_Banner">
        <var name="desktop">
            <var name="width">767</var>
            <var name="height">1152</var>
            <var name="srcset_title">Desktop </var>
            <var name="srcset_value">100vw</var>
            <var name="media">(max-width: 767px)</var>
        </var>
    </vars>    

To override existing parameters on project theme level use:

    <vars module="Ewave_Banner">
        <var name="override">
            <var name="desktop">
                <var name="width">new value width</var>
                <var name="height">new value height</var>
            </var>
        </var>
    </vars>  

### VERSION 1.1.2

* Bugfixes:
    * [#173117](https://ewave.tpondemand.com/entity/173117) -- "Play" button is not hidden when video is played - VIDEO IE 11

### VERSION 1.2.0
1. Added ability to style HTML5 Video player controls.
2. Fixed: Video in banner is not played from the beginning
3. Fixed: Video is not played automatically in the popup

### VERSION 1.3.0
1.Added data-attributes for automation testing

### VERSION 1.3.1 
1. Added compatibility with utilities settings for banner wysiwyg

### VERSION 1.3.2 

1. Fixed custom link bug
2. Removed magento serialize usage


### VERSION 2.0.0

1. Changed images upload directory. IMPORTANT: rename existing "banners" folder in pub/media

### VERSION 2.0.1
1. Removed deprecated classes usage

### VERSION 2.0.2
1. Added cache for sections

### VERSION 2.0.3
1. Fixed backend styling

### VERSION 2.0.4
1. Fixed customer segments feature for banners
2. Added manageable banners cache
3. Serialization is in one place
4. Fixed custom templates feature. Now if you specify custom template but it is not in theme directory module uses default template

### VERSION 2.0.5
1. Fix fetching customer segment for guest user

### VERSION 2.0.6
1. Renamed resource name in acl.xml

### VERSION 2.0.7
1. Added store code to cache key to fetch different content

### VERSION 2.0.8
1. Fixed bug (see changelog)
2. You can get banners json in template. If you don't want to make ajax call you can rewrite js and get banners json from block on project level ($block->getBannersJson()

### VERSION 2.1.0
1. Title attribute for video is not required

### VERSION 3.0.0
1. legobasetheme should be updated up to the latest release

### VERSION 3.0.1
1. Added ability to install extension on php7.1

### VERSION 3.0.3
1. Remove special symbols in widget.xml