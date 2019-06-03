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
    const REQUEST_DATE_FORMAT = 'dmYHi00';

    const VIRTUAL_PRODUCT_FLAG = 'Z';
    const SPECIAL_ORDER_FLAG = 'I';
    const DEFAULT_BUNCH_SIZE = 1000;
}