<?php
namespace Ewave\ProntoDigi\ProntoApi\Inventory\Get;

use Ewave\Pronto\ProntoApi\RequestBuilder as BaseRequestBuilder;
use Ewave\ProntoDigi\ProntoApi\Constants\InventoryGetRequest as RequestConstants;

/**
 * Class RequestBuilder
 * @package Ewave\ProntoDigi\ProntoApi\Inventory\Get
 */
class RequestBuilder extends BaseRequestBuilder
{
    const XML_PATH_API_INVENTORY_INTERFACE = 'ewave_pronto/api_inventory/inventory_get_uri';

    /**
     * @return $this|BaseRequestBuilder
     * @throws \Exception
     */
    protected function initRequestParams()
    {
        $params = $this->process->getRunOptions();
        if (!isset($params[RequestConstants::START_ITEM])) {
            $this->throwInvalidRequestParamsException();
        }

        if (!isset($params[RequestConstants::DATE_CHANGE_MIN])) {
            $this->throwInvalidRequestParamsException();
        }

        $this->queryParams[RequestConstants::START_ITEM] = $params[RequestConstants::START_ITEM];
        $this->queryParams[RequestConstants::DATE_CHANGE_MIN] = $params[RequestConstants::DATE_CHANGE_MIN];
        $this->requestParams = [];

        return $this;
    }
}
