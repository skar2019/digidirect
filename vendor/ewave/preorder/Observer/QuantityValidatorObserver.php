<?php
namespace Ewave\PreOrder\Observer;

/**
 * Class QuantityValidatorObserver
 * @package Ewave\PreOrder\Observer
 */
class QuantityValidatorObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Ewave\PreOrder\Model\Quote\Item\QuantityValidator
     */
    protected $quantityValidator;

    /**
     * QuantityValidatorObserver constructor.
     * @param \Ewave\PreOrder\Model\Quote\Item\QuantityValidator $quantityValidator
     */
    public function __construct(
        \Ewave\PreOrder\Model\Quote\Item\QuantityValidator $quantityValidator
    ) {
        $this->quantityValidator = $quantityValidator;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->quantityValidator->validate($observer);
    }
}
