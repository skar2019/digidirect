<?php

namespace Digidirect\AbstractGiftCard\Service\Data;

use Digidirect\AbstractGiftCard\Model\ServiceInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\ObjectFactory;

class ServiceDataObjectFactory implements ServiceDataObjectFactoryInterface
{
    /**
     * Object Manager instance
     *
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

    /**
     * Factory constructor
     *
     * @param ObjectFactory $objectFactory
     * @param Order\OrderAdapterFactory $orderAdapterFactory
     * @param Quote\QuoteAdapterFactory $quoteAdapterFactory
     */
    public function __construct(
        ObjectFactory $objectFactory,
        Session $checkoutSession
    ) {
        $this->objectFactory = $objectFactory;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Creates Service Data Object
     *
     * @param ServiceInterface $service
     * @return ServiceDataObjectInterface
     */
    public function create(ServiceInterface $service)
    {
        /**
         * @TODO add order to serviceDataObject
         */
        if ($this->_checkoutSession->getQuoteId()) {
            $data['quote'] = $this->_checkoutSession->getQuote();
        }
        $data['service'] = $service;

        return $this->objectFactory->create(
            \Digidirect\AbstractGiftCard\Service\Data\ServiceDataObject::class,
            $data
        );
    }
}
