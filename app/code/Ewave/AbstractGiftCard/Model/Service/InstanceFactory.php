<?php

namespace Ewave\AbstractGiftCard\Model\Service;

use Ewave\AbstractGiftCard\Api\Data\GiftCardServiceInterface;

/**
 * service instance factory.
 */
class InstanceFactory
{
    /**
     * @var \Ewave\AbstractGiftCard\Helper\Data
     */
    private $_helper;

    /**
     * @param \Ewave\AbstractGiftCard\Helper\Data $helper
     */
    public function __construct(
        \Ewave\AbstractGiftCard\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Create service instance.
     *
     * @param GiftCardServiceInterface $giftCardService
     * @return \Ewave\AbstractGiftCard\Model\ServiceInterface
     */
    public function create(GiftCardServiceInterface $giftCardService)
    {
        $serviceInstance = $this->_helper->getServiceInstance($giftCardService->getCode());
        $serviceInstance->setStore($giftCardService->getStoreId());

        return $serviceInstance;
    }
}
