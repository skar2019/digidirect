<?php

namespace Ewave\AbstractGiftCard\Model;

use Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface;

/**
 * Price model for external catalogs
 */
class AbstractGiftCardEntityFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @return \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface
     * @throws \UnexpectedValueException
     */
    public function create()
    {
        return $this->_objectManager->create(AbstractGiftCardEntityInterface::class);
    }
}
