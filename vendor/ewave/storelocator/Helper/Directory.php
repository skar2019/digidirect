<?php

namespace Ewave\StoreLocator\Helper;

use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\App\Helper\Context;

/**
 * @since 1.4.1
 */
class Directory extends AbstractHelper
{
    const XML_PATH_DEFAULT_COUNTRY = 'ewave_storelocator_config/list_settings/default_country';
    const XML_PATH_AVAILABLE_COUNTRIES = 'ewave_storelocator_config/list_settings/available_countries';

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regCollectionFactory;

    /**
     * @var array
     */
    protected $regionByCountry = [];

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var array
     */
    protected $countryNameByCode = [];

    /**
     * Directory constructor.
     * @param Context $context
     * @param CollectionFactory $regCollectionFactory
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $regCollectionFactory,
        CountryFactory $countryFactory
    ) {
        parent::__construct($context);
        $this->regCollectionFactory = $regCollectionFactory;
        $this->countryFactory = $countryFactory;
    }

    /**
     * @param string $countryId
     * @return array
     */
    public function getRegionsByCountry($countryId)
    {
        if (!isset($this->regionByCountry[$countryId])) {
            /** @var \Magento\Directory\Model\ResourceModel\Region\Collection $collection */
            $collection = $this->regCollectionFactory->create();
            $collection->addCountryFilter($countryId)->load();

            $regions = [];

            foreach ($collection as $region) {
                $regions[$region->getRegionId()] = [
                    'code' => $region->getCode(),
                    'name' => $region->getName()
                ];
            }
            $this->regionByCountry[$countryId] = $regions;
        }

        return $this->regionByCountry[$countryId];
    }

    /**
     * Retrieve entity country name
     *
     * @param string $countryCode
     * @return string
     */
    public function getCountryName($countryCode)
    {
        $countryName = $this->countryNameByCode[$countryCode] ?? null;
        if (!$countryName) {
            $country = $this->countryFactory->create()->loadByCode($countryCode);
            if ($country) {
                $countryName = $country->getName();
            }
            $this->countryNameByCode[$countryCode] = $countryName;

        }
        return $this->countryNameByCode[$countryCode];
    }

    /**
     * @return string
     */
    public function getDefaultCountry()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEFAULT_COUNTRY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getAvailableCountries()
    {
        return $this->getMultiSelectConfig(self::XML_PATH_AVAILABLE_COUNTRIES, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param string $path
     * @param string $scopeType
     * @param string $scopeCode
     * @return array
     */
    protected function getMultiSelectConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $value = $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
        if ($value) {
            return explode(',', $value);
        }
        return [];
    }
}
