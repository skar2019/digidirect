<?php

namespace Ewave\AbstractGiftCard\Model\Checkout\Config;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class Provider
 */
class Provider implements ConfigProviderInterface
{
    /**
     * @var \Ewave\AbstractGiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ewave\AbstractGiftCard\Helper\ServiceList
     */
    private $_serviceListHelper;

    /**
     * Provider constructor.
     * @param \Ewave\AbstractGiftCard\Helper\Data $helper
     * @param \Ewave\AbstractGiftCard\Helper\ServiceList $serviceListHelper
     */
    public function __construct(
        \Ewave\AbstractGiftCard\Helper\Data $helper,
        \Ewave\AbstractGiftCard\Helper\ServiceList $serviceListHelper
    ) {
        $this->_helper = $helper;
        $this->_serviceListHelper = $serviceListHelper;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $services = ['isDefault' => $this->_helper->isNativeGiftCardsAllowed()];
        if ($this->_helper->isActive()) {
            /**
             * @var \Ewave\AbstractGiftCard\Model\ServiceInterface $service
             */
            foreach ($this->_serviceListHelper->getAllServices() as $service) {
                $services[$service->getCode()] = ['active' => $service->isAvailable()];
            }
        }
        return ['abstractGiftCardServices' => $services];
    }
}
