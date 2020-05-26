<?php

namespace Ewave\AbstractGiftCard\Plugin\GiftCardAccount\Block\Account;

use Magento\GiftCardAccount\Block\Account\Redeem as MagentoGiftCardRedeemBlock;

class Redeem
{
    /**
     * @var \Ewave\AbstractGiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * Redeem constructor.
     * @param \Ewave\AbstractGiftCard\Helper\Data $abstractGiftCardHelper
     */
    public function __construct(\Ewave\AbstractGiftCard\Helper\Data $abstractGiftCardHelper)
    {
        $this->_helper = $abstractGiftCardHelper;
    }

    /**
     * @param MagentoGiftCardRedeemBlock $subject
     * @param callable $proceed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return null
     */
    public function aroundToHtml(MagentoGiftCardRedeemBlock $subject, callable $proceed)
    {
        if ($this->_helper->isNativeGiftCardsAllowed()) {
            return $proceed();
        }
        return null;
    }
}
