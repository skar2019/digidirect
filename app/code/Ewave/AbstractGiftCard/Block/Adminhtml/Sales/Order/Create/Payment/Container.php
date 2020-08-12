<?php

namespace Ewave\AbstractGiftCard\Block\Adminhtml\Sales\Order\Create\Payment;

use Magento\Framework\App\ObjectManager;
use Ewave\AbstractGiftCard\Model\Service\AbstractService;

class Container extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Ewave\AbstractGiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ewave\AbstractGiftCard\Api\GiftCardServiceListInterface
     */
    private $_serviceList;

    /**
     * @var \Ewave\AbstractGiftCard\Model\Service\InstanceFactory
     */
    private $_serviceInstanceFactory;

    /**
     * @var \Ewave\AbstractGiftCard\Model\Checks\SpecificationFactory
     */
    private $_serviceSpecificationFactory;

    /**
     * @var array
     */
    protected $_additionalChecks;

    /**
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    protected $_orderCreate;

    /**
     * Container constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\AbstractGiftCard\Helper\Data $helper
     * @param \Ewave\AbstractGiftCard\Model\Checks\SpecificationFactory $serviceSpecificationFactory
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Ewave\AbstractGiftCard\Api\GiftCardServiceListInterface $serviceList
     * @param \Ewave\AbstractGiftCard\Model\Service\InstanceFactory $serviceInstanceFactory
     * @param array $data
     * @param array $additionalChecks
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ewave\AbstractGiftCard\Helper\Data $helper,
        \Ewave\AbstractGiftCard\Model\Checks\SpecificationFactory $serviceSpecificationFactory,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Ewave\AbstractGiftCard\Api\GiftCardServiceListInterface $serviceList,
        \Ewave\AbstractGiftCard\Model\Service\InstanceFactory $serviceInstanceFactory,
        array $data = [],
        array $additionalChecks = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_additionalChecks = $additionalChecks;
        $this->_serviceSpecificationFactory = $serviceSpecificationFactory;
        $this->_orderCreate = $orderCreate;
        $this->_serviceInstanceFactory = $serviceInstanceFactory;
        $this->_serviceList = $serviceList;
    }

    /**
     * Prepare children blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        foreach ($this->getServices() as $service) {
            $this->setChild(
                'giftcard.service.' . $service->getCode(),
                $this->_helper->getServiceFormBlock($service, $this->_layout)
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Declare template for service form block
     *
     * @param string $method
     * @param string $template
     * @return $this
     */
    public function setServiceFormTemplate($method = '', $template = '')
    {
        if (!empty($method) && !empty($template)) {
            if ($block = $this->getChildBlock('giftcard.service.' . $method)) {
                $block->setTemplate($template);
            }
        }
        return $this;
    }

    /**
     * Check service model
     *
     * @param \Ewave\AbstractGiftCard\Model\ServiceInterface $service
     * @return bool
     */
    protected function _canUseService($service)
    {
        return true;

        /**
         * @TODO Implement Check Validation
         */

        $checks = array_merge(
            [
                AbstractService::CHECK_USE_ON_FRONT,
                AbstractService::CHECK_USE_FOR_COUNTRY,
                AbstractService::CHECK_USE_FOR_CURRENCY,
                AbstractService::CHECK_ORDER_TOTAL_MIN_MAX,
            ],
            $this->_additionalChecks
        );

        return $this->_methodSpecificationFactory->create($checks)->isApplicable(
            $service,
            $this->getQuote()
        );
    }

    /**
     * Retrieve available services
     *
     * @return array
     */
    public function getServices()
    {
        $services = $this->getData('services');
        if ($services === null) {
            $services = [];
            if ($this->_serviceList !== null) {
                $quote = $this->getQuote();
                $store = $quote ? $quote->getStoreId() : null;
                foreach ($this->_serviceList->getActiveList($store) as $service) {
                    $serviceInstance = $this->_serviceInstanceFactory->create($service);
                    if ($serviceInstance->isAvailable($quote) && $this->_canUseService($serviceInstance)) {
                        $services[] = $serviceInstance;
                    }
                }
            }
            $this->setData('services', $services);
        }
        return $services;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_orderCreate->getQuote();
    }
}
