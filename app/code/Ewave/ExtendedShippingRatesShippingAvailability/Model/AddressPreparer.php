<?php
namespace Ewave\ExtendedShippingRatesShippingAvailability\Model;

use Ewave\ExtendedShippingRates\Model\ResourceModel\Zone\CollectionFactory;
use Ewave\ExtendedShippingRates\Api\Data\ZoneInterface;

class AddressPreparer
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
     * CheckManagement constructor.
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
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @return mixed
     */
    public function prepareAddressByZone($address)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** var \Ewave\ExtendedShippingRates\Model\ResourceModel\Collection $zoneCollection */
        $zoneCollection = $this->zoneCollectionFactory->create();
        $zoneCollection->addFieldToFilter(ZoneInterface::IS_ACTIVE, 1)
            ->addStoreFilter($storeId)
            ->addPostcodeFilter($address->getPostcode());
        $zone = $zoneCollection->getFirstItem();
        if ($zone->getId()) {
            $address->setCountryId($zone->getCountryId());
            $address->setRegionId($zone->getRegionId());
        }
        return $address;
    }
}
