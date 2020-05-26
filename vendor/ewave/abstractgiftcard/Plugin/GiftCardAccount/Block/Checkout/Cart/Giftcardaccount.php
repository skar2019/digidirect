<?php

namespace Ewave\AbstractGiftCard\Plugin\GiftCardAccount\Block\Checkout\Cart;

use Magento\GiftCardAccount\Block\Checkout\Cart\Giftcardaccount as MagentoGiftCardAccountBlock;

class Giftcardaccount
{
    /**
     * @var \Ewave\AbstractGiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * Giftcardaccount constructor.
     * @param \Ewave\AbstractGiftCard\Helper\Data $abstractGiftCardHelper
     */
    public function __construct(\Ewave\AbstractGiftCard\Helper\Data $abstractGiftCardHelper)
    {
        $this->_helper = $abstractGiftCardHelper;
    }

    /**
     * @param MagentoGiftCardAccountBlock $subject
     * @param callable $proceed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return null
     */
    public function beforeToHtml(MagentoGiftCardAccountBlock $subject)
    {
        if (!$this->_helper->isNativeGiftCardsAllowed()) {
            $subject->setData('is_disabled', true);
        }
        return [];
    }
}
