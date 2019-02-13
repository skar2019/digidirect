1.0.0
=============
* Solution Architecture:
    * [#200074](https://ewave.tpondemand.com/entity/200074) -- Solution Architecture.
* New features:
    * [#199875](https://ewave.tpondemand.com/entity/199875) -- [MY STORE. SETTINGS] As an admin I want to be able to specify the following settings for My Store section
    * [#199879](https://ewave.tpondemand.com/entity/199879) -- [MY STORE. MY ACCOUNT] As a user I want to be able to view "My Store" section in My Account - Dashboard
    * [#201137](https://ewave.tpondemand.com/entity/201137) -- [MY STORE. SELECT BAR] As a user, I want to be able to use Select Bar to choose My Store

1.0.1
=============
* Bugfixes:
    * [#209132](https://ewave.tpondemand.com/entity/209132) -- My Store Select Bar not ignore "Enable "My Store" in My Account" setting
    * [#209136](https://ewave.tpondemand.com/entity/209136) -- After Click on "Change" button search field opens without created My Store entity values
    * [#209155](https://ewave.tpondemand.com/entity/209155) -- Clicking "Remove" works incorrect
    * [#209479](https://ewave.tpondemand.com/entity/209479) -- Clicking "Set My Store" link sets My Store Select Bar on focus
    * [#209533](https://ewave.tpondemand.com/entity/209533) -- [My Store Widget] Admin can select non text attr for fulltext index

1.0.2
=============
* Bugfixes:
    * Fix Wrong Block Identity output

1.1.0
=============
* New features:
    * [#215809](https://ewave.tpondemand.com/entity/215809) -- Abstract Entity.My Store. As a user, I want my Shipping Address to be verified with set up Store on checkout0
    * [#199875](https://ewave.tpondemand.com/entity/199875) -- MY STORE. SETTINGS. As an admin I want to be able to specify the following settings for My Store section
    * [#215793](https://ewave.tpondemand.com/entity/215793) -- SHIPPING ADDRESS. As a user, I want My Store to be set up from Shipping address
    * [#201137](https://ewave.tpondemand.com/entity/201137) -- MY STORE. SELECT BAR. As a user, I want to be able to use Select Bar to choose My Store
    * [#215887](https://ewave.tpondemand.com/entity/215887) -- MY STORE. AUTOCOMPLETE. As a user, I want My Store to be set up using Google Auto-complete
    * [#208894](https://ewave.tpondemand.com/entity/208894) -- MY STORE. SELECT BAR. As a user, I want to be able to use Geo Location in Select Bar

1.1.1
=============
* Improvements:
    * Remove deprecated method from Block.

1.1.2
=============
* Bugfixes:
    * [#217939](https://ewave.tpondemand.com/entity/217939) -- [PROJECT: TAF][MY STORE] Special chars are escaped in backend

1.1.3
=============
* Improvements:
    * 'searchByAddress' controller added.

1.1.4
=============
* Bugfixes:
    * [#226200](https://ewave.tpondemand.com/entity/226200) -- [My Store Widget] Can't remove Warehouse from widget
    * [#228565](https://ewave.tpondemand.com/entity/228565) -- [Advanced Pricing] Price in cart does not change after store change

1.1.5
=============
* Bugfixes:
    * [#229715](https://ewave.tpondemand.com/entity/229715) -- [PROJECT: Nick Scali][CE][My Store] "You saved the location" message is displayed even if incorrect store is submitted

2.0.0
=============
* New features:
    * [#199875](https://ewave.tpondemand.com/entity/199875) -- [MY STORE. SETTINGS] As an admin I want to be able to specify the following settings for My Store section
    * [#230170](https://ewave.tpondemand.com/entity/230170) -- [TEXT INPUT] As a user, I want My Store to be set using Text Input action
* Bugfixes:
    * [#232775](https://ewave.tpondemand.com/entity/232775) -- My Store Widget: Change engine to innodb

2.0.1
=============
* Bugfixes:
    * [#234660](https://ewave.tpondemand.com/entity/234660) -- [PROJECT: NEVERFAIL][My Store] My store is not found if search by Name attribute
    * [#236332](https://ewave.tpondemand.com/entity/236332) -- [B2B][Checkout][Product Subscriptions] Store is not reset during 1st step on checkout

2.0.2
=============
* Bugfixes:
    * [#235699](https://ewave.tpondemand.com/entity/235699) -- [PROJECT: Nick Scali][CE][My Store] store is displayed in suggest list even if it's disabled on store view level
    * [#236809](https://ewave.tpondemand.com/entity/236809) -- [Project: Ultraceuticals] Exception when mystore_entity_id cookie has non-existing value
    * [#236822](https://ewave.tpondemand.com/entity/236822) -- [My Store] "My Store Widget Stores" index is not marked as invalid and not updated on save after changing My Store settings

2.0.3
=============
* Bugfixes:
    * [#216952](https://ewave.tpondemand.com/entity/216952) -- [MY STORE. SELECT BAR] Search by attribute "State" is not working properly
    * [#237115](https://ewave.tpondemand.com/entity/237115) -- [Project: Nick Scali][CE][My Store] Incorrect request - 404 Forbidden: https://advancedpricing.lego.ewave.com/index.php/admin/ewave_abstractentity/json/AbstractEntityRecord/attribute_set_id/undefined

2.0.4
=============
* Bugfixes:
    * [#237108](https://ewave.tpondemand.com/entity/237108) -- #237108 [215139][212328] [Delivery Agent] Delivery Agent page is not opened after setting region served by agent

2.0.5
=============
* Bugfixes:
    * [#254191](https://ewave.tpondemand.com/entity/254191) -- #254191 [Project: Neverfail]: [B2B Checkout]: Session issues on Checkout: User is logged out from mini cart and shopping cart page

2.0.6
=============
* Bugfixes:
    * [#224914](https://ewave.tpondemand.com/entity/224914) -- AE My store + Advanced pricing: Advanced pricing applied, but Price and total on mini-cart is NOT updated
    * [#225504](https://ewave.tpondemand.com/entity/225504) -- [Project: Neverfail][B2B][My store]: Store is not updated after user edits Default delivery address.

2.0.7
=============
* Bugfixes:
    * [#284859](https://ewave.tpondemand.com/entity/284859) -- [PROJECT: TAF] 'Customer Care' page is broken
