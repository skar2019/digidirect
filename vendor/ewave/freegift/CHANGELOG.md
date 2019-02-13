1.0.0
=============
* New features:
    * [#126133](https://tp.ewave.com/126133) -- Free Gift. Solution Architecture
    * [#139798](https://tp.ewave.com/139798) -- GENERAL SETTINGS. As an admin, I want to be able to configure 'Free Gift' extension
    * [#139804](https://tp.ewave.com/139804) -- As a system, I want to have extra cart price rule actions
    * [#139842](https://tp.ewave.com/139842) -- As a user, I want to see free gift popup on cart page page
    * [#129596](https://tp.ewave.com/129596) -- UNIT TESTS. As a system, I want unit tests to be created for the extension

1.0.1
=============
* Bugfixes:
    * [#144164](https://tp.ewave.com/144164) -- Change Promo Items fields position on the Cart Price Rule form
    * [#144036](https://tp.ewave.com/144036) -- 3 error messages are displayed on cart instead of 1
    * [#143798](https://tp.ewave.com/143798) -- 'Free promo items for every $X' rule added promo item in two rows

1.1.0
=============
* New features:
    * [#144348](https://tp.ewave.com/144348) -- As a user, I want to be able to apply/cancel free gift rule on checkout page

1.1.1
=============
* Bugfixes:
    * [#164718](https://tp.ewave.com/126133) -- Free gift popup contains the first promo item if Type = 'One of the SKUs below'
    * [#166064](https://tp.ewave.com/126133) -- Extra settings are displayed after free gift rule was saved

1.2.0
=============
* New features:
    * [#147162](https://tp.ewave.com/147162) -- POPUP IMAGE. As a user, I want to see an image of simple product in the popup once I selected options (see description)
    * [#160167](https://tp.ewave.com/160167) -- PRODUCT DETAIL BLOCK. As a user, I want to see free gift item displayed on the product detail page
    * [#160169](https://tp.ewave.com/160169) -- ENABLE ON PRODUCT DETAIL. As an admin, I want to be able to show free items on the product detail page (see description)

1.2.1
=============
* Bugfixes:
    * [#177885](https://tp.ewave.com/177885) -- Error on di compilation
    
1.2.2
=============
* Bugfixes:
    * [#180468](https://tp.ewave.com/180468) -- Sample product popup on cart page: Impossible to scroll popup down
    * [#176897](https://tp.ewave.com/176897) -- Promo Items are not displayed on the PDP if the cart price rule is only for Not Logged In users

1.3.0
=============
* New features:
    * [#165993](https://tp.ewave.com/165993) -- BULK POPUP TEMPLATE. As a user, I want to be able to add several products to cart at once on the free gift popup
    
1.3.1
=============
* Bugfixes:
    * [#185990](https://tp.ewave.com/185990) -- Bulk Popup: Success message text doesn't match the requirements
    * [#186295](https://tp.ewave.com/186295) -- There is an error on Cart page if the Cart Price Rule with the "The same free product" action is applied
    
1.3.2
=============
* Bugfixes:
    * [#186337](https://tp.ewave.com/186337) -- Bulk Popup: The "This is a required field" validation message is displayed only for one of the required fields
    * [#186372](https://tp.ewave.com/186372) -- Bulk Popup: A wrong number of available products to be selected is displayed at the top of the popup
    * [#186658](https://tp.ewave.com/186658) -- Project: Pharmacy Online. Back-office: Cart price rule: Scheduled changes: Error displays, impossible to add date from/to fields
    * [#187048](https://tp.ewave.com/187048) -- Bulk Popup: Products don't become enabled after unchecking a product if the "One Of SKUs" cart price rule is active
    * [#187051](https://tp.ewave.com/187051) -- Bulk Popup: There are no options in the Color dropdown of a configurable product

1.3.3
=============
* Bugfixes:
    * [#187246](https://tp.ewave.com/187246) -- Bulk Popup: The 500 error occurs while adding the gift products from the second section
    * [#187265](https://tp.ewave.com/187265) -- Bulk Popup: The Size fields of Configurable product become disabled if a user checks/unchecks product in set

1.4.0
=============
* New features:
    * [#186263](https://tp.ewave.com/186263) -- As an admin, I want to see additional instructions on how to set up the extension

1.5.0
=============
* New features:
    * [#195991](https://tp.ewave.com/195991) -- [DEVELOPER] As a developer, I want to be able to identify if cart quota item is Free Gift
    * [#196304](https://tp.ewave.com/196304) -- [MESSAGE] As a user, I want to be able to see a free gift message per cart item on front end

* Bugfixes:
    * [#180101](https://tp.ewave.com/180101) -- [BACKLOG][MAGENTO BUG][Project:Pharmac Online]: Sample products: Free gift popup and the link (to the popup) will not display if msrp module is disabled
    * [#195943](https://tp.ewave.com/195943) -- [Free gift]: Cart: Free gift popup: There are no check-boxes to select free product

1.5.1
=============
* Bugfixes:
    * [#214980](https://tp.ewave.com/214980) -- [Project Pharmacy] Detail page is visible as blank

1.6.0
=============
* New features:
    * [#208973](https://tp.ewave.com/208973) -- [HIDE FRONTEND] As an admin, I want to be able to set up additional options for cart rules
* Bugfixes:
    * [#217511](https://tp.ewave.com/217511) -- [FreeGift] Type 'All SKU, One of' should be available on BE for 'Free Promo for Whole Cart'
    * [#217515](https://tp.ewave.com/217515) -- [freeGift][HideFrontend] Message 'Free gift isn't available' shouldn't be visible if setting 'Hide on Frontend=Yes'
    * [#217941](https://tp.ewave.com/217941) -- Free Gift: Discount for Product item should be calculated based on total item's qty

1.7.0
=============
* New features:
    * [#139798](https://tp.ewave.com/139798) -- [GENERAL SETTINGS] As an admin, I want to be able to configure 'Free Gift' extension
    * [#216169](https://tp.ewave.com/216169) -- As an admin, I want to be able to set up particular message for each Cart Price Rule
    * [#216170](https://tp.ewave.com/216170) -- As a user, I want to proceed to checkout through the Cart page if free product is available
    * [#219562](https://tp.ewave.com/219562) -- [2.1.8] As an extension, I should be able to work on Magento 2.1.8

* Bugfixes:
    * [#219537](https://tp.ewave.com/219537) -- [Free gift]: Prefix from cart price rule doesn't add to free gift in an order

1.7.1
=============
* Bugfixes:
    * [#220501](https://tp.ewave.com/220501) -- [Hide Frontend] Free Gift shouldn't be visible on 'Orders and Returns'

1.8.0
=============
* New features:
    * [#218870](https://tp.ewave.com/218870) -- [ADD GC TO CART] As a customer, I want Gift Card to be able to add to cart automatically

1.9.0
=============
* New features:
    * [#219564](https://tp.ewave.com/219564) -- [HIDE PRICE] As an admin, I want to be able to hide free item price on the Cart/Mini-Cart and Checkout page

1.9.1
=============
* Bugfixes:
    * [#225995](https://tp.ewave.com/225995) -- [Voucher] Error after di compilation

1.9.2
=============
* Bugfixes:
    * [#238443](https://tp.ewave.com/238443) -- [Project: Ultraceuticals] Image of voucher product is not displayed in the checkout summary block

1.9.3
=============
* Bugfixes:
    * [#252335](https://tp.ewave.com/252335) -- [Project: Ultraceuticals] Custom labels are not applied accordingly

1.9.4
=============
* Bugfixes:
    * [#255101](https://tp.ewave.com/255101) -- [Order Edit] Error message in backend: It's impossible to edit order with invoice

1.9.5
=============
* Improvements:
    * [PHP 7.1] As a system, I want the following extensions to be compatible with PHP 7.1

1.10.0
=============
* New features:
    * [#282211](https://ewave.tpondemand.com/entity/282211) -- [2.2.7] As an extension, I should be able to work on Magento 2.2.7

1.10.1
=============
* Bugfixes:
    * [#284059](https://ewave.tpondemand.com/entity/284059) -- [Free Gift]: User is NOT redirected to cart, able to complete checkout (free gift is not added to cart)
    * [#285608](https://ewave.tpondemand.com/entity/285608) -- [Free gift] Checkout page is not opened

1.10.2
=============
* Improvements:
    * Magento 2.2 compatibility
