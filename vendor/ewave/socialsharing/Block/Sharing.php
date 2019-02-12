<?php
namespace Ewave\SocialSharing\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\View\Element\Template;

class Sharing extends Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $context->getRegistry();
        parent::__construct($context, $data);
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getProduct()) {
            return '';
        }

        return parent::_toHtml();
    }
}
