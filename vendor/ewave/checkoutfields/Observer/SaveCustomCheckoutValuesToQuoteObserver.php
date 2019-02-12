<?php

namespace Ewave\CheckoutFields\Observer;

use \Magento\Framework\Event\Observer as EventObserver;
use \Magento\Framework\Event\ObserverInterface;
use \Ewave\CheckoutFields\Model\QuoteFieldValueFactory;
use \Ewave\CheckoutFields\Model\ResourceModel\OrderFieldValue\CollectionFactory as OrderFieldCollectionFactory;
use \Magento\Sales\Model\Order;
use \Magento\Quote\Model\Quote;

/**
 * Class SaveCustomCheckoutValuesToQuoteObserver
 * @package Ewave\CheckoutFields\Observer
 */
class SaveCustomCheckoutValuesToQuoteObserver implements ObserverInterface
{
    /**
     * @var QuoteFieldValueFactory
     */
    protected $_quoteFieldValueModel;

    /**
     * @var OrderFieldCollectionFactory
     */
    protected $_orderFieldsCollectionFactory;

    /**
     * SaveCustomCheckoutValuesToQuoteObserver constructor.
     * @param QuoteFieldValueFactory $quoteFieldValueModel
     * @param OrderFieldCollectionFactory $_orderFieldsCollectionFactory
     */
    public function __construct(
        QuoteFieldValueFactory $quoteFieldValueModel,
        OrderFieldCollectionFactory $_orderFieldsCollectionFactory
    ) {
        $this->_quoteFieldValueModel = $quoteFieldValueModel;
        $this->_orderFieldsCollectionFactory = $_orderFieldsCollectionFactory;
    }

    /**
     * Add delivery notes to quote object
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $model = $this->_quoteFieldValueModel->create();
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        if (!$order instanceof Order || !$quote instanceof Quote) {
            return $this;
        }
        $orderCollectionFields = $this->_orderFieldsCollectionFactory->create();
        $items = $orderCollectionFields->addFieldToFilter('order_id', $order->getId());

        $params = [];
        foreach ($items as $item) {
            $params[$item->getFieldId()] = unserialize($item->getValue());
        }
        /**
         * @var $model \Ewave\CheckoutFields\Model\QuoteFieldValue
         */
        $model->saveCustomFieldsValuesToQuote($quote, $params);
        return $this;
    }
}
