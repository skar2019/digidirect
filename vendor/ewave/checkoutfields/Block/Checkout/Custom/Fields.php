<?php

namespace Ewave\CheckoutFields\Block\Checkout\Custom;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\Json\Helper\Data;
use \Magento\Framework\View\Element\Template\Context;
use \Ewave\CheckoutFields\Model\Component\Type\AbstractType;
use \Ewave\CheckoutFields\Helper\Xml\Fields\Parser;

/**
 * Class Fields
 * @package Ewave\CheckoutFields\Block\Checkout\Custom
 */
class Fields extends Template
{
    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Parser|null
     */
    protected $parser = null;

    /**
     * Fields constructor.
     *
     * @param Context $context
     * @param array $data
     * @param Data $jsonHelper
     * @param Parser $parser
     */
    public function __construct(
        Context $context,
        Data $jsonHelper,
        Parser $parser,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonHelper = $jsonHelper;
        $this->parser = $parser;
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('custom_fields/index/index', [
            'ajax' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getFields()
    {
        $fields = $this->parser->getFields();
        $fieldsNew = [];
        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                $step = $field['area']['checkout_step'] ?? '';
                $fieldsNew[$step][$key] = $fields[$key];
            }
        }
        return $this->jsonHelper->jsonEncode($fieldsNew);
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return AbstractType::PROVIDER_VALUE;
    }
}
