<?php

namespace Digidirect\MyOrderItemsGroups\Model\UrlHandler;

use Magento\Framework\App\RequestInterface;
use Digidirect\MyOrderItems\Api\UrlHandlerInterface;
use Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\AbstractBlock;

/**
 * Class MyOrderItemsGroups
 * @package Digidirect\MyOrderItemsGroups\Model\UrlHandler
 */
class MyOrderItemsGroups implements UrlHandlerInterface
{
    /**
     * @param RequestInterface $request
     */
    public function process(RequestInterface $request)
    {
        $handlerResult = [];
        if ($page = $request->getParam(AbstractBlock::GROUP_PAGE_PARAMETER)) {
            $handlerResult['_query'][AbstractBlock::GROUP_PAGE_PARAMETER] = $page;
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
        return [AbstractBlock::GROUP_PARAMETER, AbstractBlock::GROUP_ITEM_ID];
    }
}
