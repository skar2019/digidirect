<?php

namespace Ewave\Digi\Controller\GetCart;

use LiveChat\LiveChat\Helper\Data;
use Magento\Sales\Model\Order;

class Index extends \LiveChat\LiveChat\Controller\GetCart\Index
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\ResponseInterface $responseInterface
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->_cart = $cart;
        $this->_customerSession = $customerSession;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;

        return parent::__construct($context, $cart, $customerSession, $orderCollectionFactory, $scopeConfig);
    }

    /**
     * @return |null
     */
    protected function getCustomerLastOrderRecord()
    {
        if (null === ($customerId = $this->_customerSession->getCustomer()->getId())) {
            return null;
        }

        if (!$this->_customerSession->getLastOrderRecord()) {
            $orderRecord = $this->_orderCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)
                ->addOrder('created_at', 'DESC')->fetchItem();

            if (!($orderRecord instanceof Order)) {
                return null;
            }
            $this->_customerSession->setLastOrderRecord($this->getLastOrderRecordString($orderRecord));
        }

        return $this->_customerSession->getLastOrderRecord();
    }

    /**
     * @param $orderRecord
     * @return mixed|null
     */
    protected function getLastOrderRecordString($orderRecord)
    {
        if ($orderRecord) {
            return str_replace(
                array('%createdAt%', '%updatedAt%', '%status%', '%state%', '%grandTotal%', '%currency%'),
                array(
                    $orderRecord->getData('updated_at'),
                    $orderRecord->getData('created_at'),
                    $orderRecord->getData('status'),
                    $orderRecord->getData('state'),
                    number_format(round($orderRecord->getData('grand_total'), 2), 2),
                    $orderRecord->getData('order_currency_code')
                ),
                Data::LAST_ORDER_DETAILS_PATTERN
            );
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getLastOrderDetails()
    {
        $orderRecord = $this->getCustomerLastOrderRecord();

        if ($orderRecord) {
            return $orderRecord;
        }

        return null;
    }

    /**
     * @return |null
     */
    protected function getCustomerTotalOrdersCount()
    {
        if (null === ($customerId = $this->_customerSession->getCustomer()->getId())) {
            return null;
        }

        if (!$this->_customerSession->getTotalOrdersCount()) {
            $ordersCount = $this->_orderCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)
                ->addOrder('created_at', 'DESC')->count();
            $this->_customerSession->setTotalOrdersCount($ordersCount);
        }

        return $this->_customerSession->getTotalOrdersCount();
    }

    /**
     * Returns total orders count.
     * @return integer
     */
    public function getTotalOrdersCount()
    {
        return $this->getCustomerTotalOrdersCount();
    }


}