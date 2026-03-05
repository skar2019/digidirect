<?php

namespace Digidirect\AbstractGiftCard\Model;

use Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityInterface;
use Magento\Framework\Api\ObjectFactory;

/**
 * Price model for external catalogs
 */
class AbstractGiftCardEntityFactory
{
    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @param ObjectFactory $objectFactory
     */
    public function __construct(
        ObjectFactory $objectFactory
    ) {
        $this->objectFactory = $objectFactory;
    }

    /**
     * @return AbstractGiftCardEntityInterface
     */
    public function create()
    {
        return $this->objectFactory->create(AbstractGiftCardEntityInterface::class, []);
    }
}
