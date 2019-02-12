<?php

namespace Ewave\CmsUpgrade\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ewave\CmsUpgrade\Helper\Data;

/**
 * Class BannerGrid
 * @package Ewave\CmsUpgrade\Observer
 */
class BannerGrid implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * BannerGrid constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Backend\Block\Widget\Grid $grid */
        $grid = $observer->getGrid();
        if ($grid->getId() == 'bannerGrid' && $this->_helper->isModuleOutputEnabled()) {
            $grid->getMassactionBlock()->addItem(
                'generate_upgrade_script',
                [
                    'label' => __('Generate Upgrade Script'),
                    'url' => $grid->getUrl('ewavecmsupgrade/banner/generate'),
                    'confirm' => __('Are you sure you want to generate Upgrade Script?')
                ]
            );
        }
        return $this;
    }
}
