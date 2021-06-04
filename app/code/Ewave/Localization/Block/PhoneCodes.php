<?php
namespace Ewave\Localization\Block;

use Magento\Framework\View\Element\Template;
use Ewave\Localization\Model\Configuration;

/**
 * Get json phone mask settings
 */
class PhoneCodes extends Template
{

    /**
     * @var \Ewave\Localization\Model\Configuration
     */
    protected $_configModel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Configuration $_configModel
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Configuration $_configModel,
        array $data = []
    ) {
        $this->_configModel = $_configModel;
        parent::__construct($context, $data);
    }

    /**
     * Return phone mask config
     * @return string
     */
    public function getJsonPhoneConfig()
    {
        return $this->_configModel->getPhoneJsConfig();
    }
}
