<?php

namespace Ewave\Digi\Model\Plugin\Checkout;

/**
 * Class DefaultConfigProvider
 * @package Ewave\Digi\Model\Plugin\Checkout
 */
class DefaultConfigProvider
{
    /**
     * @var \Ewave\Digi\Helper\Quote
     */
    protected $quoteHelper;

    /**
     * DefaultConfigProvider constructor.
     * @param \Ewave\Digi\Helper\Quote $quoteHelper
     */
    public function __construct(
        \Ewave\Digi\Helper\Quote $quoteHelper
    ) {
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param $result
     * @return mixed
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        $result
    ) {
        if (isset($result['quoteData'])) {
            $result['quoteData']['is_quote_has_web_only'] = $this->quoteHelper->isQuoteHasWebOnlyProducts();
        }
        return $result;
    }
}
