<?php
namespace Ewave\CollectAbstractEntity\Plugin\Magento\Quote\Model;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository as Subject;

/**
 * Class QuoteRepository
 *
 * @package Ewave\CollectAbstractEntity\Plugin\Magento\Quote\Model
 */
class QuoteRepository
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
     * @param \Magento\Quote\Model\QuoteRepository $subject
     * @param CartInterface&\Magento\Quote\Model\Quote $quote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(Subject $subject, CartInterface $quote)
    {
        $this->prefillFields->prefill($quote);
    }
}
