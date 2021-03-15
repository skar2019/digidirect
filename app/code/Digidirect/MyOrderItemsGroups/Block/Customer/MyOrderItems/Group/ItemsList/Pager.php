<?php

namespace Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList;

use Digidirect\MyOrderItems\Block\Customer\MyOrderItems\ItemsList\Pager as ParentPager;

/**
 * Class Pager
 * @package Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList
 */
class Pager extends ParentPager
{
    /**
     * @var string
     */
    protected $_pageVarName = 'pg';
}
