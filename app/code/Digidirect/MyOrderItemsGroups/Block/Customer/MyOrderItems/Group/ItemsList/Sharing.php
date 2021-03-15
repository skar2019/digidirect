<?php

namespace Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList;

use Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList;

/**
 * Class Share
 * @package Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList
 */
class Sharing extends ItemsList implements \Digidirect\SocialSharing\Api\SharingInterface
{
    /**
     * @return string
     */
    public function getSharingUrl()
    {
        return $this->_urlBuilder->getUrl(
            'myorderitems/myorderitems/share',
            [self::GROUP_PARAMETER => $this->getCurrentGroupId()]
        );
    }

    /**
     * @return bool
     */
    public function validateEntity()
    {
        return (bool)$this->getCurrentGroupId();
    }
}
