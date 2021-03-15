<?php

namespace Digidirect\MyOrderItemsGroups\Controller\Share;

/**
 * Class AddItem
 * @package Digidirect\MyOrderItemsGroups\Controller\Share
 */
class AddItem extends AddGroup
{
    /**
     * Request parameter
     */
    const ITEM_PARAMETER = 'sales_item_id';

    /**
     * @return array
     */
    protected function getSalesIds()
    {
        $salesIds = [];
        if ($saleId = $this->getRequest()->getParam(self::ITEM_PARAMETER)) {
            $salesIds = [$saleId];
        }
        return $salesIds;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getMessage()
    {
        return __(
            'The item has been added to the cart'
        );
    }
}
