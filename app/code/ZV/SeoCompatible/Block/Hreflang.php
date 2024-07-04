<?php
namespace ZV\SeoCompatible\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Registry;

class Hreflang extends Template
{
    protected $registry;
    protected $catalogHelper;

    public function __construct(
        Template\Context $context,
        CatalogHelper $catalogHelper,
        Registry $registry,
        array $data = []
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    public function getCurrentUrl()
    {
        /*$product = $this->registry->registry('current_product');
        if ($product) {
            return $this->catalogHelper->getProductUrl($product);
        }*/
        $currentUrl = rtrim($this->_urlBuilder->getCurrentUrl(), '/');
        return $currentUrl;
    }
}
