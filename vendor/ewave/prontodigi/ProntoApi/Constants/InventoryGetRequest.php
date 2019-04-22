<?php

namespace Ewave\ProntoDigi\ProntoApi\Constants;

/**
 * Class InventoryGetRequest
 * @package Ewave\ProntoDigi\ProntoApi\Constants
 */
class InventoryGetRequest extends ProductsGetRequest
{
    const DATE_CHANGE_MIN = 'date-time-change-min';
    const CHECK_WAREHOUSE_CHANGE = 'check-warehouse-change';
    const CHECK_PRICE_CHANGE = 'check-price-change';
    const REQUEST_DATE_FORMAT = 'dmY000000';

    const SWHS_SOURCE_CODE = 'SWHS';
    const DEFAULT_QTY_DECREADE_VALUE = 3;
    const SWHS_QTY_DECREADE_VALUE = 2;
    const VIRTUAL_PRODUCT_FLAG = 'Z';
    const SPECIAL_ORDER_FLAG = 'I';
    const DEFAULT_BUNCH_SIZE = 1000;
}