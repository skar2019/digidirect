<?php
namespace Digidirect\PreOrder\Observer;

/**
 * Class QuantityValidatorObserver
 * @package Digidirect\PreOrder\Observer
 */
class QuantityValidatorObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Digidirect\PreOrder\Model\Quote\Item\QuantityValidator
     */
    protected $quantityValidator;

    /**
     * QuantityValidatorObserver constructor.
     * @param \Digidirect\PreOrder\Model\Quote\Item\QuantityValidator $quantityValidator
     */
    public function __construct(
        \Digidirect\PreOrder\Model\Quote\Item\QuantityValidator $quantityValidator
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
