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
