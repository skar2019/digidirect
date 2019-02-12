<?php
namespace Ewave\Banner\Block\Adminhtml\Widget;

use Magento\Banner\Block\Adminhtml\Widget\Chooser as ChooserOriginal;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Chooser extends ChooserOriginal
{
    const URL_PATH = 'ewave_banner/widget/chooser';

    /**
     * @var \Ewave\Banner\Model\ResourceModel\Banner\Collection
     */
    protected $ewaveBannerCollection;

    /**
     * Chooser constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerColFactory
     * @param \Magento\Banner\Model\Config $bannerConfig
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Ewave\Banner\Model\ResourceModel\Banner\CollectionFactory $ewaveBannerColFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerColFactory,
        \Magento\Banner\Model\Config $bannerConfig,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Ewave\Banner\Model\ResourceModel\Banner\CollectionFactory $ewaveBannerColFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $bannerColFactory, $bannerConfig, $elementFactory, $data);
        $this->ewaveBannerCollection = $ewaveBannerColFactory->create()->addRolesColumn();
    }

    /**
     * Create grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'roles',
            [
                'header' => __('Roles'),
                'name' => 'roles',
                'index' => 'roles',
                'escape' => true,
                'filter' => false
            ]
        );
        $this->addColumnsOrder('roles', 'banner_types');

        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only banners grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            self::URL_PATH,
            [
                'banners_grid' => true,
                '_current' => true,
                'uniq_id' => $this->getId(),
                'selected_banners' => join(',', $this->getSelectedBanners())
            ]
        );
    }

    /**
     * Set banners' positions of saved banners
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->ewaveBannerCollection->addStoresVisibility();
        $this->setCollection($collection);
        \Magento\Backend\Block\Widget\Grid\Extended::_prepareCollection();

        foreach ($this->getCollection() as $item) {
            foreach ($this->getSelectedBanners() as $pos => $banner) {
                if ($banner == $item->getBannerId()) {
                    $item->setPosition($pos + 1);
                }
            }
        }
        return $this;
    }
}
