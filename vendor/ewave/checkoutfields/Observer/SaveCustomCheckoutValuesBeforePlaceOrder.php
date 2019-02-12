<?php

namespace Ewave\CheckoutFields\Observer;

use \Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveCustomCheckoutValuesBeforePlaceOrder
 * @package Ewave\CheckoutFields\Observer
 */
class SaveCustomCheckoutValuesBeforePlaceOrder implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Ewave\CheckoutFields\Helper\Xml\Fields\Parser
     */
    protected $parser;

    /**
     * @var \Ewave\CheckoutFields\Model\QuoteFieldValueFactory
     */
    protected $quoteFieldValueModel;

    /**
     * SaveCustomCheckoutValuesBeforePlaceOrder constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Ewave\CheckoutFields\Helper\Xml\Fields\Parser $parser
     * @param \Ewave\CheckoutFields\Model\QuoteFieldValueFactory $quoteFieldValueModel
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Ewave\CheckoutFields\Helper\Xml\Fields\Parser $parser,
        \Ewave\CheckoutFields\Model\QuoteFieldValueFactory $quoteFieldValueModel
    ) {
        $this->request = $request;
        $this->parser = $parser;
        $this->quoteFieldValueModel = $quoteFieldValueModel;
    }

    /**
     * Save checkout fields data before order placing
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|bool|void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return false;
        }

        $params = $this->request->getPost()->toArray();
        $fields = $this->parser->getFields();

        foreach ($params as $code => $values) {
            if (isset($fields[$code])) {
                $model = $this->quoteFieldValueModel->create();
                $model->setData([
                    'code'     => $values['label'] ?? null,
                    'quote_id' => $quote->getId(),
                    'value'    => serialize($values['value'] ?? ''),
                    'field_id' => $code,
                ]);
                $model->getResource()->save($model);
            }
        }

        return $this;
    }
}
