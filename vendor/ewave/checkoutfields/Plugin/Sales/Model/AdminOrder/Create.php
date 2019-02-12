<?php
namespace Ewave\CheckoutFields\Plugin\Sales\Model\AdminOrder;

use Ewave\CheckoutFields\Model\QuoteFieldValueFactory;
use Magento\Sales\Model\AdminOrder\Create as SubjectCreate;

/**
 * Class Create
 * @package Ewave\CheckoutFields\Plugin\Sales\Model\AdminOrder
 */
class Create
{
    /**
     * @var QuoteFieldValueFactory
     */
    protected $_quoteFieldValueModel;

    /**
     * Create constructor.
     * @param QuoteFieldValueFactory $quoteFieldValueModel
     */
    public function __construct(QuoteFieldValueFactory $quoteFieldValueModel)
    {
        $this->_quoteFieldValueModel = $quoteFieldValueModel;
    }

    /**
     * save checkout fields to quote
     *
     * @param SubjectCreate $subject
     * @param \Closure $proceed
     * @param array $data
     * @return SubjectCreate
     */
    public function aroundImportPostData(SubjectCreate $subject, \Closure $proceed, $data)
    {
        $result = $proceed($data);
        $params = isset($data['additional']) ? $data['additional'] : [];
        /** @var $model \Ewave\CheckoutFields\Model\QuoteFieldValue **/
        $model = $this->_quoteFieldValueModel->create();
        $model->saveCustomFieldsValuesToQuote($subject->getQuote(), $params);
        return $result;
    }
}
