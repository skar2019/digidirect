<?php

namespace Ewave\CheckoutFields\Plugin\Magento\Quote\Model\QuoteRepository;

use Ewave\CheckoutFields\Api\QuoteFieldValueManagementInterface;
use Ewave\CheckoutFields\Api\QuoteFieldValueRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository\LoadHandler;

/**
 * Class LoadHandlerPlugin
 *
 * @package Ewave\CheckoutFields\Plugin\Magento\Quote\QuoteRepository
 */
class LoadHandlerPlugin
{
    /**
     * @var QuoteFieldValueRepositoryInterface
     */
    protected $quoteFieldValueRepository;

    /**
     * @var QuoteFieldValueManagementInterface
     */
    protected $quoteFieldValueManagement;

    /**
     * LoadHandlerPlugin constructor.
     *
     * @param QuoteFieldValueRepositoryInterface $quoteFieldValueRepository
     * @param QuoteFieldValueManagementInterface $quoteFieldValueManagement
     */
    public function __construct(
        QuoteFieldValueRepositoryInterface $quoteFieldValueRepository,
        QuoteFieldValueManagementInterface $quoteFieldValueManagement
    ) {
        $this->quoteFieldValueRepository = $quoteFieldValueRepository;
        $this->quoteFieldValueManagement = $quoteFieldValueManagement;
    }

    /**
     * @param LoadHandler   $subject
     * @param CartInterface $quote
     *
     * @return CartInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLoad(LoadHandler $subject, CartInterface $quote)
    {
        $quoteFieldValues = $this->quoteFieldValueRepository->getListByQuoteId($quote->getId());
        $cartExtension = $this->quoteFieldValueManagement->getQuoteExtension($quote);
        $cartExtension->setQuoteFieldValues($quoteFieldValues);
        $quote->setExtensionAttributes($cartExtension);

        return $quote;
    }
}
