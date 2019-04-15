<?php

namespace Ewave\Newsletter\Block\Paypal;

use Ewave\Newsletter\Helper\Config;
use Ewave\Newsletter\Helper\Data;

/**
 * Class Review
 *
 * @package Ewave\Newsletter\Block\Paypal\Review
 */
class Review extends \Magento\Framework\View\Element\Template
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\Newsletter\Helper\Config $helperConfig
     * @param \Ewave\Newsletter\Helper\Data $helperData,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Config $helperConfig,
        Data $helperData,
        array $data = []
    ) {
        $this->config = $helperConfig;
        $this->data = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Get is hidden status
     *
     * @return bool
     */
    public function isHidden()
    {
        return ($this->config->isEnableSubscribeOnPaypal() && !$this->data->isCustomerSubscribed())?'':'hidden';
    }

    /**
     * Get label string
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->config->getNewsletterSubscribeOnCheckoutText();
    }

}
