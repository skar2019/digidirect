<?php

namespace Ewave\ProntoDigi\ProntoApi\Products\Get;

use Ewave\Pronto\ProntoApi\RequestBuilder as BaseRequestBuilder;
use Ewave\ProntoDigi\ProntoApi\Constants\ProductsGetRequest as ProductsGetRequestConstants;

/**
 * Class RequestBuilder
 *
 * @package Ewave\ProntoDigi\ProntoApi\Products\Get
 */
class RequestBuilder extends BaseRequestBuilder
{
    /**
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function initRequestParams()
    {
        $params = $this->process->getRunOptions();
        if (!isset($params[ProductsGetRequestConstants::START_ITEM])) {
            $this->throwInvalidRequestParamsException();
        }

        $this->queryParams[ProductsGetRequestConstants::START_ITEM] = $params[ProductsGetRequestConstants::START_ITEM];
        $this->requestParams = [];

        return $this;
    }
}
