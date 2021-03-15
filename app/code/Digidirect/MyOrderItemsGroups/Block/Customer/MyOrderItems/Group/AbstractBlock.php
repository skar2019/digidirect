<?php

namespace Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Block\Product\Context;
use Digidirect\MyOrderItems\Helper\Data as DataHelper;
use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItems\Block\Customer\MyOrderItems\AbstractBlock as ParentAbstractBlock;
use Digidirect\MyOrderItemsGroups\Model\OrderItemGroupRepository;
use Digidirect\MyOrderItemsGroups\Model\ItemGroupLinkRepository;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;

/**
 * Class AbstractBlock
 * @package Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems
 */
class AbstractBlock extends ParentAbstractBlock
{
    /**
     * Request parameters
     */
    const GROUP_PAGE_PARAMETER = 'pg';
    const GROUP_PARAMETER = 'group_id';
    const GROUP_ITEM_ID = 'group_item_id';

    /**
     * @var OrderItemGroupRepository
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ItemGroupLinkRepository
     */
    protected $groupLinkRepository;

    /**
     * AbstractBlock constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param OrderItemGroupRepository $groupRepository
     * @param ItemGroupLinkRepository $groupLinkRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param UrlHandlerPool $urlHandlerPool
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        OrderItemGroupRepository $groupRepository,
        ItemGroupLinkRepository $groupLinkRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UrlHandlerPool $urlHandlerPool,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupLinkRepository = $groupLinkRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct(
            $context,
            $dataHelper,
            $configHelper,
            $urlHandlerPool,
            $data
        );
    }

    /**
     * @return string
     */
    public function getPageParameter()
    {
        return self::GROUP_PAGE_PARAMETER;
    }
}
