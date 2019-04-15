<?php

namespace Ewave\ProntoDigi\ProntoApi\Products;

use Ewave\ProntoDigi\ProntoApi\ProductGetAbstract;

/**
 * Class Get
 * @package Ewave\ProntoDigi\ProntoApi\Products
 */
class Get extends ProductGetAbstract
{
    const PROCESS_CODE = 'pronto_products_get';
    const XML_PATH_API_PRODUCTS_INTERFACE = 'ewave_pronto/api_products/products_get_uri';
}
