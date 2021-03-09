<?php

namespace Digidirect\Store\Model\Store\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Store\Model\Store;

class Country extends AbstractSource implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $configCacheType;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Country factory
     *
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var State|null
     */
    private $appState;

    /**
     * @var RequestInterface|null
     */
    private $request;

    /**
     * Country constructor.
     *
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param State|null $state
     * @param RequestInterface|null $request
     */
    public function __construct(
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        State $state = null,
        RequestInterface $request = null
    ) {
        $this->countryFactory = $countryFactory;
        $this->storeManager = $storeManager;
        $this->configCacheType = $configCacheType;
        $this->appState = $state;
        $this->request = $request;
    }

    /**
     * Get list of all available countries
     *
     * If it is an admin area - check store parameter and load by it
     * otherwise load all countries
     *
     * If it is a frontend area - load by store code from store manager
     *
     * @return array
     */
    public function getAllOptions()
    {
        $area = $this->getAppState()->getAreaCode();
        $cacheKey = 'DIGIDIRECT_POS_COUNTRY_SELECT_STORE_' . $this->storeManager->getStore()->getCode() . $area;
        $storeId = (string)$this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);
        $cacheKey .= $storeId;
        if (false !== $cache = $this->configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            if ($area == Area::AREA_ADMINHTML && $storeId) {
                $collection = $this->countryFactory->create()->getResourceCollection()->loadByStore($storeId);
            } elseif ($area == Area::AREA_ADMINHTML && !$storeId) {
                $collection = $this->countryFactory->create()->getResourceCollection();
            } else {
                $collection = $this->countryFactory->create()->getResourceCollection()->loadByStore(
                    $this->storeManager->getStore()->getCode()
                );
            }
            $options = $collection->toOptionArray();
            $this->configCacheType->save(serialize($options), $cacheKey);
        }
        return $options;
    }

    /**
     * @return State|mixed|null
     */
    private function getAppState()
    {
        if (null === $this->appState) {
            $this->appState = ObjectManager::getInstance()->get(\Magento\Framework\App\State::class);
        }
        return $this->appState;
    }

    /**
     * @return RequestInterface|mixed|null
     */
    private function getRequest()
    {
        if (null === $this->request) {
            $this->request = ObjectManager::getInstance()->get(RequestInterface::class);
        }
        return $this->request;
    }
}
