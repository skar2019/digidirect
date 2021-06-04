<?php

namespace Ewave\CheckoutFields\Block\Pdp\Custom;

use Ewave\CheckoutFields\Helper\Xml\Fields\Pdp as PdpHelper;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;

/**
 * Class Fields
 * @package Ewave\CheckoutFields\Block\Pdp\Custom
 */
class Fields extends Template
{
    const ADD_TO_CART_FORM_NAME = 'product_addtocart_form';

    /**
     * @var PdpHelper
     */
    protected $_helper;

    /**
     * @var array
     */
    protected $_fields = null;

    /**
     * Fields constructor.
     * @param Context $context
     * @param PdpHelper $helper
     * @param array $data
     */
    public function __construct(Context $context, PdpHelper $helper, array $data = [])
    {
        parent::__construct($context, $data);
        $this->_helper = $helper;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        if (null === $this->_fields) {
            $this->_fields = $this->_helper->getPDPCustomFields();
        }
        return $this->_fields;
    }
}
