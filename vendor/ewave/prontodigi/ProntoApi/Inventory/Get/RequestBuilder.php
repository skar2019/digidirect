<?php
namespace Ewave\ProntoDigi\ProntoApi\Inventory\Get;

use Ewave\Pronto\ProntoApi\RequestBuilder as BaseRequestBuilder;
use Ewave\ProntoDigi\ProntoApi\Constants\InventoryGetRequest as InventoryGetRequestConstants;

/**
 * Class RequestBuilder
 * @package Ewave\ProntoDigi\ProntoApi\Inventory\Get
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * @return $this|BaseRequestBuilder
     * @throws \Exception
     */
    protected function initRequestParams()
    {
        $params = $this->process->getRunOptions();
        if (!isset($params[InventoryGetRequestConstants::START_ITEM])) {
            $this->throwInvalidRequestParamsException();
        }

        if (!isset($params[InventoryGetRequestConstants::DATE_CHANGE_MIN])) {
            $this->throwInvalidRequestParamsException();
        }

        if (!isset($params[InventoryGetRequestConstants::CHECK_WAREHOUSE_CHANGE])) {
            $this->throwInvalidRequestParamsException();
        }

        if (!isset($params[InventoryGetRequestConstants::CHECK_PRICE_CHANGE])) {
            $this->throwInvalidRequestParamsException();
        }

        $this->queryParams[InventoryGetRequestConstants::START_ITEM] = $params[InventoryGetRequestConstants::START_ITEM];
        $this->queryParams[InventoryGetRequestConstants::DATE_CHANGE_MIN] =
            $params[InventoryGetRequestConstants::DATE_CHANGE_MIN];
        $this->queryParams[InventoryGetRequestConstants::CHECK_WAREHOUSE_CHANGE] =
            $params[InventoryGetRequestConstants::CHECK_WAREHOUSE_CHANGE];
        $this->queryParams[InventoryGetRequestConstants::CHECK_PRICE_CHANGE] =
            $params[InventoryGetRequestConstants::CHECK_PRICE_CHANGE];
        $this->requestParams = [];

        return $this;
    }
}
