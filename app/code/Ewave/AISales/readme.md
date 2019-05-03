Abstract Integration Order Library
=====================

[wiki link](https://wiki.ewave.com/display/LEGO/Order+Library)

###How to use it?

```php
$this->import->saveBunch($this->getEntities());
$this->import->updateBunch($this->getEntities());
$this->import->delete('increment_id', 'store_id');
```

"$this->import" is an instance of one of the following interfaces:

```php
- Ewave\AISales\Model\Import\Order\OrderInterface - Sales Order sublib.
- Ewave\AISales\Model\Import\Invoice\InvoiceInterface - Sales Invoice sublib.
- Ewave\AISales\Model\Import\Shipment\ShipmentInterface - Sales Shipment sublib.
- Ewave\AISales\Model\Import\CreditMemo\CreditMemoInterface - Sales Creditmemo sublib.
- Ewave\AISales\Model\Import\Rma\RmaInterface - RMA sublib.
```

###Data import examples
Each interface has special validation rules you can find in the /etc/di.xml

 - Orders:

```php
public function getEntities()
{
    return [
        [
            'increment_id' => 'import_000000007', //Reguired Identify field
            'customer_id' => 3,
            'store_id' => 1, //Reguired Identify field
            'state' => 'complete',
            'status' => 'complete',
            'is_virtual' => true,
            'base_discount_amount' => 0,
            'base_grand_total' => 1000,
            'base_shipping_amount' => 10,
            'base_shipping_tax_amount' => 0,
            'base_subtotal' => 990,
            'base_tax_amount' => 0,
            'base_to_global_rate' => 1,
            'base_to_order_rate' => 1,
            'discount_amount' => 0,
            'grand_total' => 1000,
            'total_paid' => 1000,
            'base_total_paid' => 1000,
            'shipping_amount' => 10,
            'shipping_tax_amount' => 0,
            'store_to_base_rate' => 1,
            'store_to_order_rate' => 1,
            'subtotal' => 990,
            'tax_amount' => 0,
            'total_qty_ordered' => 2,
            'customer_firstname' => 'Den',
            'customer_lastname' => 'Y',
            'customer_is_guest' => true,
            'customer_note_notify' => true,
            'billing_address_id' => 1, //?????
            'customer_group_id' => 0,
            'base_shipping_discount_amount' => 0,
            'base_subtotal_incl_tax' => 990,
            'shipping_discount_amount' => 0,
            'subtotal_incl_tax' => 990,
            'weight' => 15,
            'base_currency_code' => 'USD',
            'customer_email' => 'denis.yurevich@dog.com',
            'global_currency_code' => 'USD',
            'order_currency_code' => 'USD',
            'store_currency_code' => 'USD',
            'total_item_count' => 2,
            'shipping_incl_tax' => 10,
            'base_shipping_incl_tax' => 10,
            'items' => [
                [
                    'sku' => '24-WB03', //Reguired Identify field
                    'no_discount' => true,
                    'qty_canceled' => 0,
                    'qty_invoiced' => 0,
                    'qty_ordered' => 1,
                    'qty_refunded' => 0,
                    'qty_shipped' => 0,
                    'qty_returned' => 0,
                    'price' => 400,
                    'base_price' => 400,
                    'original_price' => 400,
                    'tax_percent' => 0,
                    'tax_amount' => 0,
                    'base_tax_amount' => 0,
                    'tax_invoiced' => 0,
                    'base_tax_invoiced' => 0,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'base_discount_amount' => 0,
                    'discount_invoiced' => 0,
                    'base_discount_invoiced' => 0,
                    'amount_refunded' => 0,
                    'base_amount_refunded' => 0,
                    'row_total' => 400,
                    'base_row_total' => 400,
                    'row_invoiced' => 0,
                    'base_row_invoiced' => 0,
                    'weight' => 10,
                    'row_weight' => 10,
                ],
                [
                    'sku' => '24-WB05', //Reguired Identify field
                    'no_discount' => true,
                    'qty_canceled' => 0,
                    'qty_invoiced' => 0,
                    'qty_ordered' => 1,
                    'qty_refunded' => 0,
                    'qty_shipped' => 0,
                    'qty_returned' => 0,
                    'price' => 590,
                    'base_price' => 590,
                    'original_price' => 590,
                    'tax_percent' => 0,
                    'tax_amount' => 0,
                    'base_tax_amount' => 0,
                    'tax_invoiced' => 0,
                    'base_tax_invoiced' => 0,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'base_discount_amount' => 0,
                    'discount_invoiced' => 0,
                    'base_discount_invoiced' => 0,
                    'amount_refunded' => 0,
                    'base_amount_refunded' => 0,
                    'row_total' => 590,
                    'base_row_total' => 590,
                    'row_invoiced' => 0,
                    'base_row_invoiced' => 0,
                    'weight' => 5,
                    'row_weight' => 5,
                ],
            ],
            'payments' => [
                [
 //You can define "entity_id" field for update
                    'base_shipping_amount' => 5,
                    'shipping_amount' => 5,
                    'base_amount_ordered' => 500,
                    'amount_ordered' => 500,
                    'method' => 'checkmo',
                ],
                [
 //You can define "entity_id" field for update
                    'base_shipping_amount' => 5,
                    'shipping_amount' => 5,
                    'base_amount_ordered' => 500,
                    'amount_ordered' => 500,
                    'method' => 'checkmo',
                ],
            ],
            'billing_address' => [
 //You can define "entity_id" field for update
                'firstname' => 'Den',
                'lastname' => 'Y',
                'postcode' => '222310',
                'street' => 'Slobo',
                'city' => 'Minsk',
                'email' => 'deni@ewave.com',
                'telephone' => '1321231223',
                'country_id' => 'AU',
                'region' => 'Minsk',
            ],
            'shipping_address' => [],
            'status_histories' => [
                [
 //You can define "entity_id" field for update
                    'comment' => 'OMG! UPD',
                    'status' => 'processing',
                    'entity_name' => 'order',
                ]
            ]
        ]
    ];
}
```

 - Invoices:

```php
public function getEntities()
{
    return [
        [
            'increment_id' => 'invoice_0000001111', //Reguired Identify field
            'order_increment_id' => 'import_000000007', //Order identify field
            'store_id' => 1, //Required Identify field
            'base_grand_total' => 200,
            'shipping_tax_amount' => 0,
            'tax_amount' => 0,
            'base_tax_amount' => 0,
            'store_to_order_rate' => 1,
            'base_shipping_tax_amount' => 0,
            'base_discount_amount' => 0,
            'base_to_order_rate' => 1,
            'grand_total' => 200,
            'shipping_amount' => 10,
            'subtotal_incl_tax' => 190,
            'base_subtotal_incl_tax' => 190,
            'store_to_base_rate' => 1,
            'base_shipping_amount' => 10,
            'total_qty' => 4,
            'base_to_global_rate' => 1,
            'subtotal' => 190,
            'base_subtotal' => 190,
            'discount_amount' => 0,
            'billing_address_id' => 2,
            'state' => 2,
            'store_currency_code' => 'USD',
            'order_currency_code' => 'USD',
            'base_currency_code' => 'USD',
            'global_currency_code' => 'USD',
            'discount_tax_compensation_amount' => 0,
            'base_discount_tax_compensation_amount' => 0,
            'shipping_incl_tax' => 10,
            'base_shipping_incl_tax' => 10,
            'items' => [
                [
                    'sku' => '24-WB03', //Reguired Identify field
                    'base_price' => 25,
                    'tax_amount' => 0,
                    'base_row_total' => 51,
                    'discount_amount' => 0,
                    'row_total' => 51,
                    'base_discount_amount' => 0,
                    'price_incl_tax' => 25,
                    'base_tax_amount' => 0,
                    'base_price_incl_tax' => 0,
                    'qty' => 2,
                    'price' => 25,
                    'base_row_total_incl_tax' => 51,
                    'row_total_incl_tax' => 51,
                ],
                [
                    'sku' => '24-WB05', //Reguired Identify field
                    'base_price' => 25,
                    'tax_amount' => 0,
                    'base_row_total' => 50,
                    'discount_amount' => 0,
                    'row_total' => 50,
                    'base_discount_amount' => 0,
                    'price_incl_tax' => 25,
                    'base_tax_amount' => 0,
                    'base_price_incl_tax' => 0,
                    'qty' => 2,
                    'price' => 25,
                    'base_row_total_incl_tax' => 50,
                    'row_total_incl_tax' => 50,
                ],
            ],
            'comments' => [
                [
 //You can define "entity_id" field for update
                    'is_customer_notified' => false,
                    'is_visible_on_front' => true,
                    'comment' => 'My Comment!!!',
                ]
            ]
        ]
    ];
}
```

 - Shipments:

```php
public function getEntities()
{
    return [
        [
            'increment_id' => 'myshipment_00000011111', //Reguired Identify field
            'order_increment_id' => 'import_000000007', //Order Identify field
            'store_id' => 1, //Reguired Identify field
            'total_qty' => 8,
            'tracks' => [
                [
 //You can define "entity_id" field for update
                    'track_number' => '88888888',
                    'qty' => 1,
                    'carrier_code' => 'UPS',
                    'title' => 'UPS',
                ]
            ]
        ]
    ];
}
```

 - Creditmemo:

```php
public function getEntities()
{
    return [
        [
            'increment_id' => 'credit_0000001111144', //Reguired Identify field
            'order_increment_id' => 'import_000000007', //Order Identify field
            'store_id' => 1, //Reguired Identify field
            'base_shipping_tax_amount' => 0,
            'store_to_order_rate' => 1,
            'base_discount_amount' => 0,
            'base_to_order_rate' => 1,
            'grand_total' => 88,
            'base_subtotal_incl_tax' => 80,
            'shipping_amount' => 8,
            'subtotal_incl_tax' => 80,
            'base_shipping_amount' => 8,
            'store_to_base_rate' => 1,
            'base_to_global_rate' => 1,
            'base_adjustment' => 0,
            'base_subtotal' => 80,
            'discount_amount' => 0,
            'subtotal' => 80,
            'adjustment' => 0,
            'base_grand_total' => 88,
            'base_tax_amount' => 0,
            'shipping_tax_amount' => 0,
            'tax_amount' => 0,
            'store_currency_code' => 'USD',
            'order_currency_code' => 'USD',
            'base_currency_code' => 'USD',
            'global_currency_code' => 'USD',
            'shipping_incl_tax' => 8,
            'base_shipping_incl_tax' => 8,
            'items' => [
                [
                    'sku' => '24-WB05', //Reguired Identify field
                    'base_price' => 33,
                    'base_row_total' => 80,
                    'row_total' => 80,
                    'qty' => 2,
                    'price' => 33,
                ]
            ],
            'comments' => [
                [
 //You can define "entity_id" field for update
                    'is_customer_notified' => false,
                    'comment' => 'YYY555YY',
                ]
            ],
        ]
    ];
}
```

 - RMA:

```php
public function getEntities()
{
    return [
        [
            'increment_id' => 'rma_hard_0000000001', //Reguired Identify field
            'order_increment_id' => 'import_000000007', //Order Identify field
            'store_id' => 1, //Reguired Identify field
            'status' => 'pending',
            'is_active' => true,
            'date_requested' => '2016-05-18',
            'customer_id' => 9,
            'items' => [
                [
                    'product_sku' => '24-WB05', //Reguired Identify field
                    'status' => 'pending',
                    'qty_requested' => 1,
                    'condition' => 'Opened',
                    'reason' => 'Wrong Color',
                    'reason_other' => '',
                    'resolution' => 109,
                ]
            ],
            'comments' => [
 //You can define "entity_id" field for update
            ],
        ]
    ];
}
```
