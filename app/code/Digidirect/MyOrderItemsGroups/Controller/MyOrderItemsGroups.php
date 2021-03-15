<?php

namespace Digidirect\MyOrderItemsGroups\Controller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;
use Digidirect\MyOrderItemsGroups\Model\OrderItemGroupRepository;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterfaceFactory;
use Digidirect\MyOrderItems\Controller\MyOrderItems;
use Digidirect\MyOrderItemsGroups\Helper\Data as DataHelper;
use Digidirect\MyOrderItemsGroups\Helper\Config as ConfigHelper;

/**
 * Class MyOrderItemsGroups
 * @package Digidirect\MyOrderItemsGroups\Controller
 */
abstract class MyOrderItemsGroups extends MyOrderItems
{
    /**
     * @var OrderItemGroupRepository
     */
    protected $groupRepository;

    /**
     * @var OrderItemGroupInterfaceFactory
     */
    protected $orderItemGroupFactory;

    /**
     * @var UrlHandlerPool
     */
    protected $urlHandlerPool;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * MyOrderItemsGroups constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param OrderItemGroupRepository $groupRepository
     * @param OrderItemGroupInterfaceFactory $orderItemGroupFactory
     * @param UrlHandlerPool $urlHandlerPool
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        OrderItemGroupRepository $groupRepository,
        OrderItemGroupInterfaceFactory $orderItemGroupFactory,
        UrlHandlerPool $urlHandlerPool,
        DataHelper $dataHelper,
        ConfigHelper $configHelper
    ) {
        $this->groupRepository = $groupRepository;
        $this->urlHandlerPool = $urlHandlerPool;
        $this->orderItemGroupFactory = $orderItemGroupFactory;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
        parent::__construct($context, $customerSession);
    }

    /**
     * @param array $exclude
     * @return array
     */
    protected function collectParams(array $exclude = [])
    {
        return $this->urlHandlerPool->execute($this->getRequest(), $exclude);
    }
}
