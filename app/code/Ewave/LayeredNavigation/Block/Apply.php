<?php
namespace Ewave\LayeredNavigation\Block;

class Apply extends \Magento\Framework\View\Element\Template
{
    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * @var \Ewave\LayeredNavigation\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Ewave\LayeredNavigation\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Ewave\LayeredNavigation\Helper\Data $helper,
        array $data = []
    ) {
        $this->catalogLayer = $layerResolver->get();
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Check availability display apply button
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return (($this->helper->isApplyButtonEnabled() || $this->helper->isMobileApplyButtonEnabled())
            && ($this->catalogLayer->getProductCollection()->getSize()
                || !empty($this->helper->getSelectedFiltersSettings())));
    }

    /**
     * @return bool
     */
    public function showApplyButtonOnMobileOnly()
    {
        return !$this->helper->isApplyButtonEnabled() && $this->helper->isMobileApplyButtonEnabled();
    }
}
