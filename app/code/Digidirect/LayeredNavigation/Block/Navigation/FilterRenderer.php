<?php
namespace Digidirect\LayeredNavigation\Block\Navigation;

use Digidirect\LayeredNavigation\Api\Data\FilterSettingInterface;
use Digidirect\LayeredNavigation\Helper\FilterSetting;
use Digidirect\LayeredNavigation\Helper\UrlBuilder;
use Digidirect\LayeredNavigation\Helper\UrlParser;
use Digidirect\LayeredNavigation\Model\Layer\Filter\Item;
use Digidirect\LayeredNavigation\Model\Source\DisplayMode;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @var FilterSetting
     */
    protected $settingHelper;

    /**
     * @var UrlBuilder
     */
    protected $urlBuilder;

    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * FilterRenderer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param FilterSetting $settingHelper
     * @param UrlBuilder $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        FilterSetting $settingHelper,
        UrlBuilder $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->settingHelper = $settingHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter)
    {
        $this->assign('filterItems', $filter->getItems());
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }
}
