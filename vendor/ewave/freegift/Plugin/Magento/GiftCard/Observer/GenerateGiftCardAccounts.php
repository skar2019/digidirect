<?php

namespace Ewave\FreeGift\Plugin\Magento\GiftCard\Observer;

use Magento\GiftCard\Observer\GenerateGiftCardAccounts as OriginalGenerateGiftCardAccounts;
use Magento\Framework\Registry;

class GenerateGiftCardAccounts
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * GenerateGiftCardAccounts constructor.
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->_registry = $registry;
    }

    /**
     * @param OriginalGenerateGiftCardAccounts $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @return OriginalGenerateGiftCardAccounts
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        OriginalGenerateGiftCardAccounts $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer
    ) {
        $flag = \Ewave\FreeGift\Plugin\Magento\Sales\Model\Order\Item::FIX_FREE_GIFT_GIFTCARD_AMOUNT_FLAG;
        $this->_registry->register($flag, true, true);
        $return = $proceed($observer);
        $this->_registry->unregister($flag);
        return $return;
    }
}
