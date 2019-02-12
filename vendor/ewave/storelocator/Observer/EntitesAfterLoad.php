<?php

namespace Ewave\StoreLocator\Observer;

use Ewave\StoreLocator\Helper\Config;
use Ewave\StoreLocator\Model\EntitiesSorter;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ObserverInterface;
use Ewave\StoreLocator\Model\CoordinatesProviderFactory;

class EntitesAfterLoad implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var EntitiesSorter
     */
    protected $entitiesSorter;

    /**
     * EntitesAfterLoad constructor.
     * @param Config $configHelper
     * @param EntitiesSorter $entitiesSorter
     */
    public function __construct(Config $configHelper, EntitiesSorter $entitiesSorter)
    {
        $this->configHelper = $configHelper;
        $this->entitiesSorter = $entitiesSorter;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->configHelper->isGroupSearchResultByParentEntity()) {
            $resultObject = $observer->getResultObject();
            if ($resultObject instanceof DataObject) {
                $sortedData = $this->entitiesSorter->sort($resultObject->getData());
                $resultObject->addData($sortedData);
            }
        }

        return $this;
    }

}
