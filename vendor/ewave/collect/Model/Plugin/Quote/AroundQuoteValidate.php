<?php

namespace Ewave\Collect\Model\Plugin\Quote;

use Closure;
use Ewave\Collect\Exception\CollectPlaceQtyException;
use Ewave\Collect\Model\Carrier\Collectcarrier;

class AroundQuoteValidate
{

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Source Processor Factory
     *
     * @var \Ewave\Collect\Model\SourceProcessor
     */
    protected $_sourceProcessorFactory;

    /**
     * Collect Helper
     * 
     * @var \Ewave\Collect\Helper\Data
     */
    protected $_collectHelper;

    /**
     * CollectQuantityValidator
     *
     * @var \Ewave\Collect\Model\CollectQuantityValidator
     */
    protected $_collectQuantityValidator;

    /**
     * AroundQuoteValidate constructor.
     * 
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ewave\Collect\Helper\Data $collectHelper
     * @param \Ewave\Collect\Helper\Config\Data $collectConfigHelper
     * @param \Ewave\Collect\Model\CollectQuantityValidator $collectQuantityValidator
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Ewave\Collect\Helper\Data $collectHelper,
        \Ewave\Collect\Helper\Config\Data $collectConfigHelper,
        \Ewave\Collect\Model\CollectQuantityValidator $collectQuantityValidator
    ) {
        $this->_logger = $logger;
        $this->_collectHelper = $collectHelper;
        $this->_collectConfigHelper = $collectConfigHelper;
        $this->_collectQuantityValidator = $collectQuantityValidator;
    }

    /**
     * AroundValidateBeforeSubmit
     *
     * @param \Magento\Quote\Model\QuoteValidator $qtyValidator
     * @param Closure $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\QuoteValidator
     * @throws CollectPlaceQtyException
     */
    public function aroundValidateBeforeSubmit(
        \Magento\Quote\Model\QuoteValidator $qtyValidator,
        Closure $proceed,
        \Magento\Quote\Model\Quote $quote
    ) {
        $this->checkShippingMethod($quote);
        $qtyValidator = $proceed($quote);
        if ($this->_collectHelper->isCollectEnable() && $this->_collectHelper->hasStockUpdateInterface()) {
            $quoteItems = $quote->getAllVisibleItems();
            foreach ($quoteItems as $quoteItem) {
                try {
                    if ($quoteItem->getCollectPlaceId()) {
                        $checkQty = $this->_collectQuantityValidator->checkProductQtyInSource(
                            $quoteItem,
                            $quoteItem->getCollectPlaceId()
                        );

                        if (!$checkQty) {
                            $message = __('Not enough quantity for item SKU = %1', $quoteItem->getSku());
                            $this->_logger->warning($message);
                            throw new CollectPlaceQtyException(
                                $message
                            );
                        }
                    }
                } catch (\Exception $e) {
                    $this->_collectHelper->logError($e->getMessage());
                    return $qtyValidator;
                }
            }
        }

        return $qtyValidator;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return void
     */
    public function checkShippingMethod($quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getShippingMethod() &&
            $this->_collectHelper->isCollectItems($quote->getId()) &&
            !$this->_collectHelper->isDeliveryItems($quote->getId())
        ) {
            $shippingAddress->setShippingMethod(Collectcarrier::COLLECT_SHIPPING_METHOD);
        }
    }
}
