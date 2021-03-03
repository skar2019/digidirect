### Description
The extension allows create menu items and menu sets and assign them to menu sets
Menu set is area to assign menu items. Menu set can be assigned to specific store or all store views

### VERSION 1.0.0
  1. Manage Menu Sets(Status, Store View, Code, Title)

  2. Manage Menu Items :
  - type : Cms Block, Category, Custom Link - [GLOBAL setting]
  - Menu Set: multiple select, required - [GLOBAL setting]
  - link(for cms blocks and custom links) - [STORE VIEW setting]
  - category (for category type) - [STORE VIEW setting]
  - Parent Menu Item (optional) - [STORE VIEW setting]
  - Position - [STORE VIEW setting]
  - For logged in users - [STORE VIEW setting]
  3. Specify custom options. Useful for developers to customize menu items styling

[GLOBAL setting] - you are not able to manage it per store. Changing these settings affects all stores
[STORE VIEW setting] - you are able to manage it per store. For example title in default store ="Default store title", in first website = "First Website title" 
  
##### How to display menu on frontend

For example if we created menu set maned header:

            <block class="Digidirect\Navigation\Block\Menu" name="digidirect.menu.header" as="digidirect_menu_header" template="menu_static.phtml">
                <arguments>
                    <argument name="area_selector" xsi:type="string">navigation-wrapper</argument>
                    <argument name="wrapper_class" xsi:type="string">menu-wrapper</argument>
                    <argument name="item_class" xsi:type="string">item</argument>
                    <argument name="item_label_class" xsi:type="string">link</argument>
                    <argument name="inner_lists_class" xsi:type="string">menu</argument>
                    <argument name="sub_menu_block_class" xsi:type="string">sub-menu</argument>
                    <argument name="cms_block_class_name" xsi:type="string">cms</argument>
                    <argument name="expanded" xsi:type="string">false</argument>
                    <argument name="place" xsi:type="string">main</argument>
                    <argument name="horizontal" xsi:type="string">true</argument>
                    <argument name="static" xsi:type="boolean">true</argument>
                    <argument name="action" xsi:type="string">click</argument>
                    <argument name="responsive" xsi:type="boolean">true</argument>
                    <argument name="toggler_selector" xsi:type="string">[data-action="toggle-nav"]</argument>
                    <argument name="off_canvas_class" xsi:type="string">offcanvas-open</argument>
                    <argument name="off_canvas_side" xsi:type="string">left</argument>
                    <argument name="off_canvas_event" xsi:type="string">click</argument>
                    <argument name="breakpoint" xsi:type="string">768px</argument>
                    <argument name="view_custom" xsi:type="string">Digidirect_Navigation/js/dist/views/custom/index</argument>
                    <argument name="link_string" xsi:type="string">All {original}</argument>
                    <argument name="add_link_to_top" xsi:type="boolean">false</argument>
                </arguments>
            </block>

set_code - Set Code to be displayed on frontend. If you don't specify set code you'll get empty json string

### VERSION 1.0.1

  1. Bugfix #164144 PROJECT: Coke Vending. MOBILE. background is scrolled when scrolling expanded burger menu
  2. Bugfix #164537 Project: Coke Vending. Frontend issues

### VERSION 1.0.2
  1.Bugfix #169989 PROJECT: Pharmacy Online. Category dropdown: Arrow on dropdown is not clickable
  2.Bugfix #166269 WIDGET IN MENU. As a user, I want to see abstract options widget embed to CMS block in navigation if added
  3.Bugfix #169815 PROJECT: Pharmacy Online. Tablet: Clicking on Category dropdown page reloads
  4.Bugfix #171307 PROJECT: Pharmacy Online: Category Dropdown: Selected category is not "active"
  5.Bugfix #169192 Navigation. All level sub menus are expanded on hovering over parent item
 
### VERSION 1.0.3 
  1. Added ability to assign menu item to specified store
  2. Custom link/CMS block menu types - field "link" is not required
  3. Added set code and parent menu item name to grid
  4. Added ACL filters for grid and create/edit menu item actions
  5. Bugfix #171498 PROJECT: Pharmacy Online: Navigation. Contact Us: Menu item is not active after submitting contatc us form
  6. Bugfix #172695 Impossible to select menu items for the third level (Hover Action)
  7. Bugfix #173486 Menu dropdown is NOT close when user moves mouse cursor out from dropdown area
  8. Bugfix #173502 TABLET: Main menu: Dropdown is NOT closing on second click on menu item
  
  Database structure changes:
      1. Added "digidirect_navigation_menu_item_store_relation" table. Now user can assign menu item to specified store
  
### VERSION 1.0.4
  1. Bugfix #183104 - Navigation - Category menu link is not clickable if category with subcategories selected
  2. Bugfix #180972 - Links not working
  3. Bugfix #179873 - Menu Item name: The input value is cleared after keyup event
    
### VERSION 1.1.0
  1. Added ability to add phone link
  2. Admin able to see menu item set name in parent dropdown in backend  
  
### VERSION 1.1.1
  1. Added data-attributes to some tags for automation testing
  
### VERSION 1.1.2
  1. Fixed merge js error
  
### VERSION 1.1.3
  1. Fixed multi-root categories drop-down
  
### VERSION 1.1.5
  1. Added blocks cache for menu
  
### VERSION 1.1.6
  1. Fixed block caching issue, now current url works correctly
  
### VERSION 1.2.0
  1. Added assigned set code and store view name when manage parent menu item
  2. Now you can use $block->getMenuAsArray() method to get menu items array (not json which is useless if you use static menu)
  
### VERSION 1.2.1
  1. Fixed custom link when link contains more then 3 "/"
  2. Menu now is cached
  3. Optimized urls comparing
  
### VERSION 1.3.0
  1. Added new widget type - Navigation Set. Has default template and it is also possible to use custom templates
  2. Added refactored template file. You don't need to use json_decode in template and use $item->property anymore.
   Now use $item[property]. This file is menu_static_array.phtml
   Old file menu_static.phtml left for backward compatibility for legacy projects
  3. As widget has been introduced it is supposed that it is better to use it than old way displaying it on frontend
  4. Improved getIdentities() method in block class.
  
### VERSION 1.3.1
  1. Js logic issue fixed
  2. Improved performance of categories list
  3. improved content filtering process
  4. removed array_push
  5. removed extra str_repeat usage 
 