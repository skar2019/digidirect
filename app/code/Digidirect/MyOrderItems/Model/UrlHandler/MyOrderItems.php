<?php

namespace Digidirect\MyOrderItems\Model\UrlHandler;

use Magento\Framework\App\RequestInterface;
use Digidirect\MyOrderItems\Api\UrlHandlerInterface;
use Digidirect\MyOrderItems\Block\Customer\MyOrderItems\AbstractBlock;

/**
 * Class MyOrderItems
 * @package Digidirect\MyOrderItems\Model\UrlHandler
 */
class MyOrderItems implements UrlHandlerInterface
{
    /**
     * Request parameters
     */
    const PAGE_PARAMETER = 'p';
    const CATEGORY_PARAMETER = 'category_id';

    /**
     * @param RequestInterface $request
     */
    public function process(RequestInterface $request)
    {
        $handlerResult = [];
        if ($page = $request->getParam(self::PAGE_PARAMETER)) {
            $handlerResult['_query'][self::PAGE_PARAMETER] = $page;
        }
        foreach ($this->getParams() as $param) {
            if ($value = $request->getParam($param)) {
                $handlerResult = array_merge($handlerResult, [$param => $value]);
            }
        }
        return $handlerResult;
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        return [self::CATEGORY_PARAMETER, AbstractBlock::ITEM_PARAMETER];
    }
}
