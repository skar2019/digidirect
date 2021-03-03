 ## Navigation Menu Settings

 Option | Type | Default | Description
 ------ | ---- | ------- | -----------
 area | string | '.navigation-wrapper' |  Selector element where menu will be rendered
 wrapperClass | string | '.menu-wrapper' | Outter class of menu
 itemClass | string | '.item' | Defines selector of item.
 innerListsClass | string | '.menu' | Dropdowns in inner blocks class name
 subMenuBlockClass | string | 'sub-menu' | Inner block class name
 expanded | boolean | false | Defines inner blocks expands or not
 horizontal | boolean | true | Horizontal menu or not
 action | string | hover | Do we use hover/click logic
 responsive | boolean | true | Defines do we implement offcanvas logic
 togglerSelector | string | '[data-action="toggle-nav"]' | Selector of element, which opens offcanvas
 offcanvasClass | string | 'offcanvas-open' | Class which added to html when trigger toggler
 offCnavasSide | string | 'left' | Side of offcanvas
 offCanvasEvent | string | 'click' | Event, on which we binds toggler handler
 breakpoint | string | '768px' | Width of screen on which offcanvas logic started to work
 linkString | string | 'All {original}' | Configure label of added link on click, where {original}, is text of copied link
 addLinkToTop | boolean | false | Choose place to add link.

 Need to remove default magento navigation from theme folder after adding module to it.
 To do it you need to remove importing of _navigation.less in /lib/_lib.less and /source/_sources.less files of in <Vendor>/<your_theme>/<web> directory