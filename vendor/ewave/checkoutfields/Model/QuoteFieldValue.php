<?php

namespace Ewave\CheckoutFields\Model;

use \Magento\Framework\DataObject\IdentityInterface;
use \Ewave\CheckoutFields\Api\Data\QuoteFieldValueInterface;
use \Magento\Framework\Model\AbstractModel;
use \Ewave\CheckoutFields\Helper\Xml\Fields\Parser;

/**
 * Class QuoteFieldValue
 * @package Ewave\CheckoutFields\Model
 */
class QuoteFieldValue extends AbstractModel implements IdentityInterface, QuoteFieldValueInterface
{
    const CACHE_TAG = 'ewave_checkout_fields';

    /**
     * @var string
     */
    protected $_eventPrefix = 'ewave_checkoutfields_quote';

    /**
     * @var Parser
     */
    protected $_parser;

    /**
     * QuoteFieldValue constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Parser $parser
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Parser $parser,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_parser = $parser;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue');
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
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
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
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
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
     * Delete quote values after saving to order
     *
     * @param int $quoteId
     * @return void
     */
    public function cleanDataBeforeSave($quoteId)
    {
        $items = $this->getCollection()->addFieldToFilter('quote_id', $quoteId);
        /**
         * @var $item $this
         */
        foreach ($items as $item) {
            $this->_getResource()->delete($item);
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $params
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function saveCustomFieldsValuesToQuote($quote, $params = [])
    {
        $this->cleanDataBeforeSave($quote->getId());
        $dataToSave = [];
        $fields = $this->_parser->getFields($quote->getStoreId());
        foreach ($params as $code => $values) {
            $label = $fields[$code]['frontend_name'] ?? '';
            $dataToSave[] = [
                'code' => $label,
                'quote_id' => $quote->getId(),
                'value' => serialize($values ?? ''),
                'field_id' => $code,
            ];
        }
        if (!empty($dataToSave)) {
            $this->_getResource()->saveCustomFieldsValuesToQuote($dataToSave);
        }
    }
}
