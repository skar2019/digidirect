<?php
namespace Digidirect\Catalog\Block;

class MetaTitle extends \Magento\Framework\View\Element\Template
{
    public function __construct(
            \Magento\Framework\View\Element\Template $context,
            \Magento\Framework\View\Page\Config $pageConfig,
            array $data = []
    ) {
        $this->pageConfig = $pageConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Test New Meta Title!')); // browser tab title
        return $this;
    }
}
