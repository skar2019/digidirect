<?php

namespace Ewave\CheckoutFields\Observer;

use \Magento\Framework\Event\Observer as EventObserver;
use \Magento\Framework\Event\ObserverInterface;
use \Ewave\CheckoutFields\Model\OrderFieldValueFactory;
use \Magento\Sales\Model\Order;
use \Magento\Quote\Model\Quote;

/**
 * Class SaveCustomCheckoutValuesToOrderObserver
 * @package Ewave\CheckoutFields\Observer
 */
class SaveCustomCheckoutValuesToOrderObserver implements ObserverInterface
{
    /**
     * @var OrderFieldValueFactory
     */
    protected $orderFieldValueModel;

    /**
     * @var Collection
     */
    protected $quoteCollection;

    /**
     * SaveCustomCheckoutValuesToOrderObserver constructor.
     * @param OrderFieldValueFactory $orderFieldValueModel
     */

    /**
     * SaveCustomCheckoutValuesToOrderObserver constructor.
     * @param OrderFieldValueFactory $orderFieldValueModel
     */
    public function __construct(OrderFieldValueFactory $orderFieldValueModel)
    {
        $this->orderFieldValueModel = $orderFieldValueModel;
    }

    /**
     * Add delivery notes from quote to order object
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        if (!$order instanceof Order || !$quote instanceof Quote) {
            return $this;
        }

        $model = $this->orderFieldValueModel->create();
        /**
         * @var $model \Ewave\CheckoutFields\Model\OrderFieldValue
         */
        $model->saveCustomCheckoutValuesToOrder($quote, $order);
        return $this;
    }
}
