<?php

namespace Ewave\CheckoutFields\Model;

use \Magento\Framework\DataObject\IdentityInterface;
use \Ewave\CheckoutFields\Api\Data\OrderFieldValueInterface;
use \Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use \Magento\Checkout\Model\Session;
use \Ewave\CheckoutFields\Api\Data\QuoteFieldValueInterface;

/**
 * Class OrderFieldValue
 *
 * @package Ewave\CheckoutFields\Model
 */
class OrderFieldValue extends AbstractModel implements IdentityInterface, OrderFieldValueInterface
{
    const CACHE_TAG = 'ewave_checkout_fields';

    /**
     * @var string
     */
    protected $_eventPrefix = 'ewave_checkoutfields_order';

    /**
     * @var ResourceModel\QuoteFieldValue\CollectionFactory
     */
    protected $_quoteFieldsCollectionFactory;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * OrderFieldValue constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Session $checkoutSession
     * @param ResourceModel\QuoteFieldValue\CollectionFactory $quoteFieldsCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Session $checkoutSession,
        \Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue\CollectionFactory $quoteFieldsCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_quoteFieldsCollectionFactory = $quoteFieldsCollectionFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\CheckoutFields\Model\ResourceModel\OrderFieldValue');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @return string|null
     */
    public function getFieldId()
    {
        return $this->getData(self::FIELD_ID);
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @param string|array $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @param string $fieldId
     * @return $this
     */
    public function setFieldId($fieldId)
    {
        return $this->setData(self::FIELD_ID, $fieldId);
    }

    /**
     * Save quote custom fields to order
     *
     * @param Quote $quote
     * @param Order $order
     * @return void
     */
    public function saveCustomCheckoutValuesToOrder(Quote $quote, Order $order)
    {
        /**
         * @var $items \Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue\Collection
         */
        $items = $this->prepareQuoteCollectionFields($quote->getId());

        if (!$items->count()) {
            // Index controller was not fired yet
            $this->checkoutSession->setObserverFlag(true);
        }

        $dataToSave = [];

        foreach ($items as $key => $item) {
            $dataToSave[$key] = [
                'code' => $item->getCode(),
                'order_id' => $order->getId(),
                'value' => $item->getValue(),
                'field_id' => $item->getFieldId()
            ];
        }

        if (!empty($dataToSave)) {
            $this->_getResource()->saveCustomCheckoutValuesToOrder($dataToSave);
            $this->deleteItems($items);
        }
    }

    /**
     * @param int $quoteId
     * @return ResourceModel\QuoteFieldValue\Collection
     */
    public function prepareQuoteCollectionFields($quoteId)
    {
        /* @var $quoteCollectionFields \Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue\Collection */
        $quoteCollectionFields = $this->_quoteFieldsCollectionFactory->create();
        return $quoteCollectionFields->addFieldToFilter(QuoteFieldValueInterface::QUOTE_ID, $quoteId);
    }

    /**
     * @param \Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue\Collection $items
     * @throws \Exception
     */
    public function deleteItems($items)
    {
        /**
         * @var $item \Ewave\CheckoutFields\Model\QuoteFieldValue
         */
        foreach ($items as $item) {
            $item->getResource()->delete($item);
        }
    }

    /**
     * Get custom fields order
     *
     * @param int $orderId
     * @return \Ewave\CheckoutFields\Model\ResourceModel\OrderFieldValue\Collection
     */
    public function getCustomFields($orderId)
    {
        return $this->getCollection()->addFieldToFilter(self::ORDER_ID, ['eq' => $orderId]);
    }
}
