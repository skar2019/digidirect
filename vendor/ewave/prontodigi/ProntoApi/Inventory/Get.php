<?php

namespace Ewave\ProntoDigi\ProntoApi\Inventory;

use Ewave\Pronto\ProntoApi\RequestBuilderInterface;
use Ewave\Pronto\ProntoApi\ResponseHandlerMultipleInterface;
use Ewave\ProntoDigi\ProntoApi\Constants\InventoryGetRequest as InventoryGetRequestConstants;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Ewave\ProntoDigi\ProntoApi\ProductGetAbstract;

/**
 * Class Get
 * @package Ewave\ProntoDigi\ProntoApi\Inventory
 */
class Get extends ProductGetAbstract
{
    const PROCESS_CODE = 'pronto_inventory_get';
    const XML_PATH_API_INVENTORY_INTERFACE = 'ewave_pronto/api_inventory/inventory_get_uri';
    const CHECK_CHANGE_VALUE = 'Y';

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Get constructor.
     * @param DateTime $date
     * @param RequestBuilderInterface $requestBuilder
     * @param ResponseHandlerMultipleInterface|null $responseHandler
     * @param null $initParams
     */
    public function __construct(
        DateTime $date,
        RequestBuilderInterface $requestBuilder,
        ResponseHandlerMultipleInterface $responseHandler = null,
        $initParams = null
    ) {
        parent::__construct($requestBuilder, $responseHandler, $initParams);
        $this->dateTime = $date;
    }

    /**
     * @return $this|\Ewave\ProntoDigi\ProntoApi\Products\Get
     */
    protected function reInitRunOptions()
    {
        $minDate = $this->dateTime->date(InventoryGetRequestConstants::REQUEST_DATE_FORMAT);
        parent::reInitRunOptions();
        $this->_runOptions = array_merge($this->_runOptions, [
            InventoryGetRequestConstants::DATE_CHANGE_MIN => $minDate,
            InventoryGetRequestConstants::CHECK_WAREHOUSE_CHANGE => self::CHECK_CHANGE_VALUE,
            InventoryGetRequestConstants::CHECK_PRICE_CHANGE => self::CHECK_CHANGE_VALUE
        ]);
        return $this;
    }
}
