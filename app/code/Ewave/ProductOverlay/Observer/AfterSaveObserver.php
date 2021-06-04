<?php

namespace Ewave\ProductOverlay\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Ewave\ProductOverlay\Model\Overlay\Attribute\Source\Status;
use Ewave\ProductOverlay\Api\Data\OverlayInterface;

/**
 * Class AfterSaveObserver
 * @package Ewave\ProductOverlay\Observer
 */
class AfterSaveObserver implements ObserverInterface
{
    /**
     * @var \Ewave\ProductOverlay\Model\Overlay\Product\AttributeManager
     */
    protected $attributeManager;

    /**
     * AfterSaveObserver constructor.
     * @param \Ewave\ProductOverlay\Model\Overlay\Product\AttributeManager $attributeManager
     */
    public function __construct(\Ewave\ProductOverlay\Model\Overlay\Product\AttributeManager $attributeManager)
    {
        $this->attributeManager = $attributeManager;
    }

    /**
     * Remove product attribute value if overlay was disabled
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $overlay = $observer->getEvent()->getObject();
        if ($overlay->getOrigData(OverlayInterface::STATUS) != $overlay->getStatus()
            && $overlay->getStatus() == Status::STATUS_DISABLED) {
            $this->attributeManager->clearProductAttributeValue($overlay);
        }
        return $this;
    }
}
