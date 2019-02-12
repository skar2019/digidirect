<?php

namespace Ewave\CheckoutFields\Controller\Index;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use \Ewave\CheckoutFields\Model\QuoteFieldValueFactory;
use \Ewave\CheckoutFields\Model\OrderFieldValueFactory;
use \Ewave\CheckoutFields\Helper\Xml\Fields\Parser;
use \Magento\Checkout\Model\Session;
use \Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue\Collection;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Ewave\Utilities\Helper\Data as UtilitiesHelper;

/**
 * Class Index
 * @package Ewave\CheckoutFields\Controller\Index
 */
class Index extends Action
{
    const FULL_ACTION_NAME = 'ewave_checkout_fields_index_index';

    /**
     * @var QuoteFieldValueFactory
     */
    protected $quoteFieldValueModel;

    /**
     * @var OrderFieldValueFactory
     */
    protected $orderFieldValueModel;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Collection
     */
    protected $quoteCollection;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var UtilitiesHelper
     */
    protected $utilitiesHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param QuoteFieldValueFactory $quoteFieldValueModel
     * @param OrderFieldValueFactory $orderFieldValueModel
     * @param Parser $parser
     * @param Session $checkoutSession
     * @param Collection $collection
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param UtilitiesHelper $utilitiesHelper
     */
    public function __construct(
        Context $context,
        QuoteFieldValueFactory $quoteFieldValueModel,
        OrderFieldValueFactory $orderFieldValueModel,
        Parser $parser,
        Session $checkoutSession,
        Collection $collection,
        JsonFactory $resultJsonFactory,
        UtilitiesHelper $utilitiesHelper
    ) {
        parent::__construct($context);
        $this->quoteFieldValueModel = $quoteFieldValueModel;
        $this->orderFieldValueModel = $orderFieldValueModel;
        $this->parser = $parser;
        $this->checkoutSession = $checkoutSession;
        $this->quoteCollection = $collection;
        $this->jsonFactory = $resultJsonFactory;
        $this->utilitiesHelper = $utilitiesHelper;
    }

    /**
     * Save custom checkout fields to custom tables
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $success = true;
        $error = false;
        if (!$this->getRequest()->isAjax() || !$params = $this->getRequest()->getPost()->toArray()) {
            return $this->_sendJsonResponse(['success' => $success, 'error' => $error]);
        }

        $fields = $this->parser->getFields();

        if ($this->checkoutSession->getObserverFlag()) {
            $this->checkoutSession->unsObserverFlag();
            // We should have a new order ID already

            $order = $this->checkoutSession->getLastRealOrder();
            if ($order && $order->getId()) {
                // we can store field directly in order_field_value
                foreach ($params as $code => $values) {
                    try {
                        if (isset($fields[$code])) {
                            $model = $this->orderFieldValueModel->create();
                            $value = $values['value'] ?? '';
                            $frontendInput = $fields[$code]['frontend_input'] ?? '';
                            $model->setData([
                                'code' => $values['label'] ?? null,
                                'order_id' => $order->getId(),
                                'value' => serialize($this->prepareTextValue($value, $frontendInput)),
                                'field_id' => $code,
                            ]);
                            $model->getResource()->save($model);
                        }
                    } catch (\Exception $e) {
                        $error = $e->getMessage();
                        $success = false;
                    }
                }
                return $this->_sendJsonResponse(['success' => $success, 'error' => $error]);
            }
        }

        /**
         * @var $model \Ewave\CheckoutFields\Model\QuoteFieldValue
         */
        $model = $this->quoteFieldValueModel->create();
        $quote = $this->checkoutSession->getQuote();

        if (!$quoteId = $quote->getId()) {
            try {
                $quoteId = $this->checkoutSession->getLastRealOrder()->getQuoteId();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong with session.'));
            }
        }

        $model->cleanDataBeforeSave($quoteId);

        foreach ($params as $code => $values) {
            try {
                $model = $this->quoteFieldValueModel->create();
                if (isset($fields[$code])) {
                    $value = $values['value'] ?? '';
                    $frontendInput = $fields[$code]['frontend_input'] ?? '';
                    $model->setData([
                        'code' => $values['label'] ?? null,
                        'quote_id' => $quoteId,
                        'value' => serialize($this->prepareTextValue($value, $frontendInput)),
                        'field_id' => $code,
                    ]);
                    $model->getResource()->save($model);
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $success = false;
            }
        }

        return $this->_sendJsonResponse(['success' => $success, 'error' => $error]);
    }

    /**
     * Get json represented result
     *
     * @param [] $data
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function _sendJsonResponse($data)
    {
        return $this->jsonFactory->create()->setData($data);
    }

    /**
     * Prepare Text Value to serialization
     *
     * @param string $value
     * @param string $fieldType
     * @return string
     */
    protected function prepareTextValue($value, $fieldType)
    {
        if (!empty($value) && in_array($fieldType, ['text', 'textarea'])) {
            $value = $this->utilitiesHelper->replace4byte($value);
        }

        return $value;
    }
}
