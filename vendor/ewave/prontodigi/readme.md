Pronto Integration for DigiDirect project
=============================

[wiki link](https://wiki.ewave.com/display/LEGO/%5BPronto%5D+DigiDirect)

1.0.0
=============
* New features:
    * [#289110](https://ewave.tpondemand.com/entity/289110) -- Product Interface
    * [#289111](https://ewave.tpondemand.com/entity/289111) -- Product Interface DigiDirect custom rules
    * [#293327](https://ewave.tpondemand.com/entity/293327) -- Inventory & Pricing Interface
    * [#294027](https://ewave.tpondemand.com/entity/294027) -- Create Order Interface
    * [#294039](https://ewave.tpondemand.com/entity/294039) -- Create Order Interface DigiDirect custom rules
    * [#294028](https://ewave.tpondemand.com/entity/294028) -- Order Status Update Interface
    * [#294029](https://ewave.tpondemand.com/entity/294029) -- Order Details Update Interface
    * [#302978](https://ewave.tpondemand.com/entity/302978) -- update an Invoice increment ID and send an Invoice email

1.0.1
=============
* Bugfixes:
    * [#310732](https://ewave.tpondemand.com/entity/310732) -- Product Interface: Integration run overwrites product's description field
* Improvements:
    * [#289111](https://ewave.tpondemand.com/entity/289111) -- Set description only for new products

1.0.2
=============
* New features:
    * [#312031](https://ewave.tpondemand.com/entity/312031) -- Inventory & Pricing Interface: Set Source.qty buffer configuration
* Bugfixes:
    * [#311208](https://ewave.tpondemand.com/entity/311208) -- Order Interface: Orders placed via ZipPay are not synced with Pronto in BE

1.0.3
=============
* Bugfixes:
    * [#313425](https://ewave.tpondemand.com/entity/313425) -- Order Interface: Pickup orders in store are being charged to the wrong customer

1.0.4
=============
* New features:
    * [#312910](https://ewave.tpondemand.com/entity/312910) -- Assign product to the all level categories, not to the last level category.

1.0.5
=============
* Bugfixes:
    * [#315910](https://ewave.tpondemand.com/entity/315910) -- Orders with "&" failing

1.0.6
=============
* New features:
    * [#319664](https://ewave.tpondemand.com/entity/319664) -- Order Interface: Have a credit card type information in 'Transaction ID'

1.0.7
=============
* Bugfixes:
    * [#317607](https://ewave.tpondemand.com/entity/317607) -- Uncaught exceptions displayed during order interface run

1.0.8
=============
* New features:
    * [#321608](https://ewave.tpondemand.com/entity/321608) -- Product Interface: Products with 'stk-condition' = 'O' should be disabled after integration

1.0.9
=============
* New features:
    * [#325699](https://ewave.tpondemand.com/entity/325699) -- Order Interface: Add new changes to order interface

1.0.10
=============
* New features:
    * [#327099](https://ewave.tpondemand.com/entity/327099) -- Order Interface: Send orders with M2E Pro Payment method

* Bugfixes:
    * [#325912](https://ewave.tpondemand.com/entity/325912) -- Product Interface: reverts product priority