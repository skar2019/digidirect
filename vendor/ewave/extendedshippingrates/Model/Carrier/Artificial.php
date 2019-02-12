<?php
namespace Ewave\ExtendedShippingRates\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Ewave\ExtendedShippingRates\Api\Data\MethodInterface;

/**
 * Class Artificial
 * @package Ewave\ExtendedShippingRates\Model\Carrier
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Artificial extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = null;

    /**
     * @var \Ewave\ExtendedShippingRates\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var \Ewave\ExtendedShippingRates\Model\ResourceModel\Carrier\CollectionFactory
     */
    protected $_carrierCollectionFactory;

    /**
     * @var \Ewave\ExtendedShippingRates\Model\ResourceModel\Carrier\Collection
     */
    protected $_carriersCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var array
     */
    protected $_loadedCarriers = [];

    /**
     * @var RateRequest
     */
    protected $_request;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Ewave\ExtendedShippingRates\Model\CarrierFactory $carrierFactory
     * @param \Ewave\ExtendedShippingRates\Model\ResourceModel\Carrier\CollectionFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Ewave\ExtendedShippingRates\Model\CarrierFactory $carrierFactory,
        \Ewave\ExtendedShippingRates\Model\ResourceModel\Carrier\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
    
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_carrierFactory = $carrierFactory;
        $this->_carrierCollectionFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->prepareCarriers();
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        $this->setRequest($request);

        $result = [];
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier $carrier */
        $carrier = $this->findCarrier();
        if (!$carrier) {
            return $result;
        }

        $this->addData($carrier->getData());
        $this->_code = $carrier->getData('carrier_code');

        $methods = $carrier->getMethods();
        if (empty($methods)) {
            return $result;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method $methodData */
        foreach ($methods as $methodData) {
            if (!$methodData->getActive()) {
                continue;
            }
            $methodData->afterLoad();
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->getId());
            $method->setCarrierTitle($carrier->getTitle());
            $method->setMethod($methodData->getData(MethodInterface::CODE));
            $method->setCost($methodData->getData(MethodInterface::COST));
            $method->setAlternativeTitle($methodData->getData(MethodInterface::ALTERNATIVE_TITLE));
            $method->setAlternativeCode($methodData->getData(MethodInterface::ALTERNATIVE_CODE));
            $method = $this->applyRates($method, $methodData);

            if ($method) {
                $result->append($method);
            }
        }

        return $result;
    }

    /**
     * @param RateRequest|null $_request
     * @return $this
     */
    protected function setRequest(RateRequest $_request = null)
    {
        $this->_request = $_request;

        return $this;
    }

    /**
     * @return RateRequest
     */
    protected function getRequest()
    {
        return $this->_request;
    }

    /**
     * Find corresponding carrier in the collection
     *
     * @return \Ewave\ExtendedShippingRates\Model\Carrier|null
     */
    protected function findCarrier()
    {
        $carrier = $this->_carrierFactory->create();
        if ($this->getOnlyActive() &&
            $carrier->getResource() instanceof \Ewave\ExtendedShippingRates\Model\ResourceModel\Carrier) {
            $carrier->getResource()->setOnlyActiveMethodsFlag();
        }
        $carrier->load($this->getData('id'), 'carrier_code');

        return $carrier;
    }

    /**
     * Get all data of the carrier specified by code (carrier_code)
     * It's possible to get the specified parameter ($param) of the carrier
     *
     * @param string $code
     * @param null $param
     * @return mixed|null
     */
    protected function getSpecificCarrierData($code, $param = null)
    {
        $item = $this->_carriersCollection->getItemByColumnValue('carrier_code', $code);
        if (!$item) {
            return null;
        }

        if (!$param) {
            return $item->getData();
        }

        return $item->getData($param);
    }

    /**
     * Prepare carriers collection & load items
     *
     * @return void
     */
    protected function prepareCarriers()
    {
        if (empty($this->_loadedCarriers)) {
            /** @var \Ewave\ExtendedShippingRates\Model\ResourceModel\Carrier\Collection $carriersCollection */
            $this->_carriersCollection = $this->_carrierCollectionFactory->create();
            $this->_loadedCarriers = $this->_carriersCollection->getItems();
        }
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        $carrier = $this->findCarrier();
        if (!$carrier) {
            return [];
        }

        return $carrier->getMethodsCollection()->toAllowedMethodsArray();
    }

    /**
     * Get allowed shipping methods as array of objects
     * @return array
     */
    public function getAllowedMethodCollectionArray()
    {
        $carrier = $this->findCarrier();
        if (!$carrier) {
            return [];
        }

        return $carrier->getMethodsCollection()->toAllowedMethodsArray(false);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $method
     * @param \Ewave\ExtendedShippingRates\Model\Carrier\Method $methodData
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method|null
     */
    protected function applyRates(
        \Magento\Quote\Model\Quote\Address\RateResult\Method $method,
        \Ewave\ExtendedShippingRates\Model\Carrier\Method $methodData
    ) {
        $disableMethodWithoutValidRates = false;
        $request = $this->getRequest();
        $rates = $methodData->getRates();
        $ratesApplied = [];

        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate $rate */
        foreach ($rates as $rate) {
            if (!$rate->validateRequest($request)) {
                continue;
            }
            $ratesApplied[] = $rate;
        }

        if ($ratesApplied) {
            $filteredRates = $this->filterRatesBeforeApply($ratesApplied, $request, $methodData);
            /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate $validRate */
            foreach ($filteredRates as $validRate) {
                $method = $validRate->applyRateToMethod($method, $request, $methodData);
            }
        } elseif ($disableMethodWithoutValidRates) {
            return null;
        } else {
            $method->setMethodTitle($methodData->getData('title'));
            $method->setPrice($methodData->getData('price'));
        }

        return $method;
    }

    protected function filterRatesBeforeApply(
        $rates,
        RateRequest $request,
        \Ewave\ExtendedShippingRates\Model\Carrier\Method $methodData
    ) {
        if (!$rates) {
            return $rates;
        }

        $multipleRatesCalculationType = $this->_storeManager
            ->getStore()
            ->getConfig('ewave_extendedshippingrates/main/multiple_rates_price');

        switch ($multipleRatesCalculationType) {
            case \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate::MULTIPLE_RATES_PRICE_CALCULATION_MAX_PRIORITY:
                $resultRate = $this->getRateWithMaxPriority($rates);
                break;
            case \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate::MULTIPLE_RATES_PRICE_CALCULATION_MAX_PRICE:
                $resultRate = $this->getRateWithMaxPrice($rates, $request, $methodData);
                break;
            case \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate::MULTIPLE_RATES_PRICE_CALCULATION_MIN_PRICE:
                $resultRate = $this->getRateWithMinPrice($rates, $request, $methodData);
                break;
            default:
                return $rates;
        }

        $resultRates = [$resultRate->getId() => $resultRate];

        return $resultRates;
    }

    /**
     * Find rate with max priority in array of rates
     *
     * @param array $rates
     * @return \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate
     */
    protected function getRateWithMaxPriority($rates)
    {
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate $currentRate */
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate $rate */
        foreach ($rates as $currentRate) {
            if (!isset($rate) || $rate->getPriority() <= $currentRate->getPriority()) {
                $rate = $currentRate;
            }
        }

        return $rate;
    }

    /**
     * Find rate with max price in array of rates
     *
     * @param array $rates
     * @param RateRequest $request
     * @param \Ewave\ExtendedShippingRates\Model\Carrier\Method $methodData
     * @return \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate
     */
    protected function getRateWithMaxPrice(
        $rates,
        RateRequest $request,
        \Ewave\ExtendedShippingRates\Model\Carrier\Method $methodData
    ) {
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate $currentRate */
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate $rate */
        $actualRateCalculatedPrice = 0;
        foreach ($rates as $currentRate) {
            $currentRatePrice = $currentRate->getCalculatedPrice($request, $methodData);
            if (!isset($rate) || $actualRateCalculatedPrice <= $currentRatePrice) {
                $rate = $currentRate;
                $actualRateCalculatedPrice = $currentRatePrice;
            }
        }

        return $rate;
    }

    /**
     * Find rate with min price in array of rates
     *
     * @param array $rates
     * @param RateRequest $request
     * @param \Ewave\ExtendedShippingRates\Model\Carrier\Method $methodData
     * @return \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate
     */
    protected function getRateWithMinPrice(
        $rates,
        RateRequest $request,
        \Ewave\ExtendedShippingRates\Model\Carrier\Method $methodData
    ) {
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate $currentRate */
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate $rate */
        $actualRateCalculatedPrice = 0;
        foreach ($rates as $currentRate) {
            $currentRatePrice = $currentRate->getCalculatedPrice($request, $methodData);
            if (!isset($rate) || $actualRateCalculatedPrice >= $currentRatePrice) {
                $rate = $currentRate;
                $actualRateCalculatedPrice = $currentRatePrice;
            }
        }

        return $rate;
    }

    /**
     * @param string $field
     * @return false|mixed|string
     */
    public function getConfigData($field)
    {
        $carrier = $this->findCarrier();
        if ($carrier) {
            return $carrier->getData($field);
        }
        return parent::getConfigData($field);
    }
}
