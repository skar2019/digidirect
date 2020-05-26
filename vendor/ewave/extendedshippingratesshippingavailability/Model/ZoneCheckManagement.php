<?php

namespace Ewave\ExtendedShippingRatesShippingAvailability\Model;

use Ewave\ExtendedShippingRates\Model\ResourceModel\Zone\CollectionFactory;
use Ewave\ExtendedShippingRates\Api\Data\ZoneInterface;

/**
 * Class ZoneCheckManagement
 * @package Ewave\ExtendedShippingRatesShippingAvailability\Model
 */
class ZoneCheckManagement
{
    /**
     * @var CollectionFactory
     */
    protected $zoneCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ZoneCheckManagement constructor.
     * @param CollectionFactory $zoneCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $zoneCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->zoneCollectionFactory = $zoneCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $postcode
     * @return \Ewave\ExtendedShippingRates\Api\Data\ZoneInterface;
     */
    public function getZoneByPostcode($postcode)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /**
         * var \Ewave\ExtendedShippingRates\Model\ResourceModel\Collection $zoneCollection
         */
        $zoneCollection = $this->zoneCollectionFactory->create();
        $zoneCollection->addFieldToFilter(ZoneInterface::IS_ACTIVE, 1)
            ->addStoreFilter($storeId)
            ->addPostcodeFilter($postcode);
        $zone = $zoneCollection->getFirstItem();
        return $zone;
    }
}
