<?php
namespace Ewave\CollectAbstractEntity\Plugin\Ewave\Collect\Helper\Config;

use Ewave\Collect\Helper\Config\Address as Subject;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class Address
 *
 * @package Ewave\CollectAbstractEntity\Plugin\Ewave\Collect\Helper\Config
 */
class Address
{
    /**
     * @var \Ewave\CollectAbstractEntity\Model\PrefillFields
     */
    protected $prefillFields;

    /**
     * QuoteRepository constructor.
     *
     * @param \Ewave\CollectAbstractEntity\Model\PrefillFields $prefillFields
     */
    public function __construct(
        \Ewave\CollectAbstractEntity\Model\PrefillFields $prefillFields
    ) {
        $this->prefillFields = $prefillFields;
    }

    /**
     * @param Subject $subject
     * @param \Closure $closure
     * @param \Magento\Quote\Model\Quote\Address $address
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return \Magento\Quote\Model\Quote\Address
     * @see \Ewave\Collect\Helper\Config\Address::applyDummyAddress
     */
    public function aroundApplyDummyAddress(
        Subject $subject,
        \Closure $closure,
        \Magento\Quote\Model\Quote\Address $address
    ) {
        if ($this->prefillFields->prefill($address->getQuote())) {
            return $address;
        }

        return $closure($address);
    }
}
