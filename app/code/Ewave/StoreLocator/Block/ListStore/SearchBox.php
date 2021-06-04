<?php

namespace Ewave\StoreLocator\Block\ListStore;

use \Magento\Config\Model\Config\Source\Locale\Country;
use \Magento\Directory\Helper\Data as DirectoryHelper;
use \Magento\Framework\View\Element\Template\Context;
use Ewave\StoreLocator\Helper\Config as ConfigHelper;
use Magento\Directory\Model\CountryFactory;
use Ewave\Googleapi\Helper\Config as GoogleApiHelper;

class SearchBox extends \Ewave\StoreLocator\Block\AbstractBlock
{
    const SEARCH_URL_PATH = 'ewave_storelocator/index/search';

    /**
     * @var Country
     */
    protected $localCountry;

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * SearchBox constructor.
     * @param Context $context
     * @param ConfigHelper $configHelper
     * @param DirectoryHelper $directoryHelper
     * @param Country $localCountry
     * @param CountryFactory $countryFactory
     * @param GoogleApiHelper $googleApiConfigHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigHelper $configHelper,
        DirectoryHelper $directoryHelper,
        Country $localCountry,
        CountryFactory $countryFactory,
        GoogleApiHelper $googleApiConfigHelper,
        array $data = []
    ) {
        $this->directoryHelper = $directoryHelper;
        $this->localCountry = $localCountry;
        parent::__construct($context, $configHelper, $countryFactory, $googleApiConfigHelper, $data);

    }

    /**
     * @return string
     */
    public function getRegionJson()
    {
        return $this->directoryHelper->getRegionJson();
    }

    /**
     * @return array
     */
    public function getCountryOption()
    {
        return $this->localCountry->toOptionArray();
    }

    /**
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->getUrl(self::SEARCH_URL_PATH);
    }

    /**
     * @return bool|int|null
     */
    public function getCacheLifetime()
    {
        $cacheLifeTime = parent::getCacheLifetime();
        if (!$cacheLifeTime) {
            $cacheLifeTime = 86400;
        }

        return $cacheLifeTime;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKey = parent::getCacheKeyInfo();
        $cacheKey['nil'] = $this->getNameInLayout();
        $cacheKey['rq'] = json_encode($this->getRequest()->getParams());
        return $cacheKey;
    }
}
