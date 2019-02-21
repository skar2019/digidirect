<?php
namespace Ewave\ExtendedCatalogPriceRule\Block;

use Ewave\ExtendedCatalogPriceRule\Helper\Data;
use Magento\Framework\View\Element\Template;

/**
 * Class ExtendedRule
 * @package Ewave\ExtendedCatalogPriceRule\Block
 */
class ExtendedRule extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @param float $amount
     * @return float|string
     */
    public function formatDiscountAmount($amount)
    {
        return $this->helper->formatDiscountAmountAsHtml($amount);
    }

    /**
     * @param string $description
     * @return string
     */
    public function prepareDescription($description)
    {
        return $this->helper->prepareContent($description);
    }
}
