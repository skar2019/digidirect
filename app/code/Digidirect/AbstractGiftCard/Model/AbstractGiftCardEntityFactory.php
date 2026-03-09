<?php

namespace Digidirect\AbstractGiftCard\Model;

use Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityInterface;
use Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityInterfaceFactory;

/**
 * Price model for external catalogs
 */
class AbstractGiftCardEntityFactory
{
    /**
     * @var AbstractGiftCardEntityInterfaceFactory
     */
    private $entityFactory;

    /**
     * @param AbstractGiftCardEntityInterfaceFactory $entityFactory
     */
    public function __construct(AbstractGiftCardEntityInterfaceFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    /**
     * @return \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityInterface
     * @throws \UnexpectedValueException
     */
    public function create()
    {
        return $this->entityFactory->create();
    }
}
