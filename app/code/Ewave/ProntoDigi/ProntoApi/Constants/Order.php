<?php

namespace Ewave\ProntoDigi\ProntoApi\Constants;

class Order
{
    const ROOT_CONTAINER = 'sales-orders';
    const SALES_ORDER = 'sales-order';
    const HEADER = 'header';
    const ACCOUNT_NAME = 'accountname';
    const ACCOUNT = 'account';
    const ORDER_DATE = 'order-date';
    const WAREHOUSE = 'warehouse';
    const CUSTOMER_TYPE = 'customer-type';
    const REP = 'rep';
    const TERRITORY = 'territory';
    const REFERENCE = 'reference';
    const SO_DELIVERY_DATE = 'so-delivery-date';
    const CONTRACT_NAME = 'contactname';
    const EMAIL = 'email';
    const ORDER_TOTAL_INC_TAX = 'order-total-inc-tax';
    const SET_ON_STATUS = 'set-on-status';
    const SO_PART_SHIPMENT_ALLOWED = 'so-part-shipment-allowed';
    const SO_ORDER_PRIORITY = 'so-order-priority';
    const SO_CUST_TYPE = 'so-cust-type';
    const BILLING_ADDRESS = 'billing-address';
    const DELIVERY_ADDRESS = 'delivery-address';
    const DELIVERY_INSTRUCTION = 'delivery-instruction';
    const PAYMENT_DETAILS = 'payment-details';
    const DETAIL = 'detail';
    const ON_HOLD_REASON_CODE = 'on-hold-reason-code';

    const ATTRIBUTE_PRONTO_STATUS_CODE = 'pronto_status_code';
    const ATTRIBUTE_PRONTO_ORDER_NUMBER = 'pronto_order_number';
    const ATTRIBUTE_PRONTO_ORDER_TRACKING_NUMBER = 'pronto_order_tracking_number';
    const ATTRIBUTE_PRONTO_MANIFEST_NUMBER = 'pronto_manifest_number';
    
    const QFF = 'QFF';
    const QFF_SURNAME = 'QFFSURNAME';
    
    const CUSTOM_DATA = 'Custom-data';
    const DATA = 'data';
    const KEY = 'key';
    const VALUE = 'value';
}
