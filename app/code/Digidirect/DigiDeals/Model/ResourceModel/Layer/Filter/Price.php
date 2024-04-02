<?php

namespace Digidirect\DigiDeals\Model\ResourceModel\Layer\Filter;

class Price extends \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Digidirect\DigiDeals\Model\Layer $layer,
        $connectionName = null
        ) {
            $this->layer        = $layerResolver->get();
            $this->session      = $session;
            $this->storeManager = $storeManager;
            parent::__construct(
                $context,
                $eventManager,
                $layerResolver,
                $session,
                $storeManager,
                $connectionName
            );
    }
}