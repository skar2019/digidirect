<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace LatitudeNew\Payment\Block\System\Config;

/**
 * Generate checkbox for admin form
 */
class Checkbox extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected const CONFIG_PATH = 'payment/latitudepay/payment_terms';

    /**
     * @var string
     */
    protected $_template = 'LatitudeNew_Payment::system/config/checkbox.phtml';

    /**
     * Possible values for the checkboxes
     */
    protected $_values = null;

    /**
     * @var \LatitudeNew\Payment\Model\Config\Source\Term
     */
    private $termSource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \LatitudeNew\Payment\Model\Config\Source\Term $termSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->termSource = $termSource;
    }
   
    /**
     * Retrieve element HTML markup.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());

        return $this->_toHtml();
    }
    
    /**
     * Get checkbox values
     */
    public function getValues()
    {
        $values = [];

        foreach ($this->termSource->toOptionArray() as $value) {
            $values[$value['value']] = $value['label'];
        }

        return $values;
    }

    /**
     * Whether a checkbox is checked
     * 
     * @param  string $name 
     * @return boolean
     */
    public function isChecked($name)
    {
        return in_array($name, $this->getCheckedValues());
    }

    /**
     * Get the checked value from config
     */
    public function getCheckedValues()
    {
        if ($this->_values === null) {
            $data = $this->getConfigData();
            if (isset($data[self::CONFIG_PATH])) {
                $data = $data[self::CONFIG_PATH];
            } else {
                $data = '';
            }
            $this->_values = explode(',', $data);
        }

        return $this->_values;
    }
}
