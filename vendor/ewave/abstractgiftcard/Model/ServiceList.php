<?php

namespace Ewave\AbstractGiftCard\Model;

use Magento\Framework\App\ObjectManager;
use Ewave\AbstractGiftCard\Model\Service\AbstractService;

class ServiceList
{
    /**
     * @var \Ewave\AbstractGiftCard\Model\Checks\SpecificationFactory
     */
    protected $_serviceSpecificationFactory;

    /**
     * @var \Ewave\AbstractGiftCard\Api\GiftCardServiceListInterface
     */
    private $_serviceList;

    /**
     * @var \Ewave\AbstractGiftCard\Model\Service\InstanceFactory
     */
    private $_serviceInstanceFactory;

    /**
     * @param \Ewave\AbstractGiftCard\Model\Checks\SpecificationFactory $specificationFactory
     */
    public function __construct(
        \Ewave\AbstractGiftCard\Model\Checks\SpecificationFactory $specificationFactory
    ) {
        $this->_serviceSpecificationFactory = $specificationFactory;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Ewave\AbstractGiftCard\Model\ServiceInterface[]
     * @api
     */
    public function getAvailableServices(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $store = $quote ? $quote->getStoreId() : null;
        $availableServices = [];

        foreach ($this->_getGiftCardServiceList()->getActiveList($store) as $service) {
            $serviceInstance = $this->_getGiftCardServiceInstanceFactory()->create($service);
            if ($serviceInstance->isAvailable($quote) && $this->_canUseService($serviceInstance, $quote)) {
                $availableServices[] = $serviceInstance;
            }
        }
        return $availableServices;
    }

    /**
     * Check service model
     *
     * @param \Ewave\AbstractGiftCard\Model\ServiceInterface $service
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return bool
     */
    protected function _canUseService($service, \Magento\Quote\Api\Data\CartInterface $quote)
    {
        return $this->_serviceSpecificationFactory->create(
            [
                AbstractService::CHECK_USE_CHECKOUT,
                AbstractService::CHECK_USE_FOR_COUNTRY,
                AbstractService::CHECK_USE_FOR_CURRENCY,
                AbstractService::CHECK_ORDER_TOTAL_MIN_MAX,
            ]
        )->isApplicable(
            $service,
            $quote
        );
    }

    /**
     * Get service list.
     *
     * @return \Ewave\AbstractGiftCard\Api\GiftCardServiceListInterface
     */
    private function _getGiftCardServiceList()
    {
        if ($this->_serviceList === null) {
            $this->_serviceList = ObjectManager::getInstance()->get(
                \Ewave\AbstractGiftCard\Api\GiftCardServiceListInterface::class
            );
        }
        return $this->serviceList;
    }

    /**
     * Get service instance factory.
     *
     * @return \Ewave\AbstractGiftCard\Model\Service\InstanceFactory
     */
    private function _getGiftCardServiceInstanceFactory()
    {
        if ($this->_serviceInstanceFactory === null) {
            $this->_serviceInstanceFactory = ObjectManager::getInstance()->get(
                \Ewave\AbstractGiftCard\Model\Service\InstanceFactory::class
            );
        }
        return $this->serviceInstanceFactory;
    }
}
