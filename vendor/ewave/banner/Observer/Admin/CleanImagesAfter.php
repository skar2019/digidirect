<?php

namespace Ewave\Banner\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;
use Ewave\Banner\Model\Image\Cache;

/**
 * Images cache cleaner.
 * When user clicks "Flush catalog images cache" it also flushes banners cache
 *
 * @since 1.0.0
 * @since 3.0.0 extra parameters were removed from di
 */
class CleanImagesAfter implements ObserverInterface
{
    /**
     * @var \Ewave\Banner\Model\Image\Cache
     */
    protected $imageCache;

    /**
     * CleanImagesAfter constructor.
     *
     * @param \Ewave\Banner\Model\Image\Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->imageCache = $cache;
    }

    /**
     * @param $observer \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->imageCache->cleanImagesCache();
    }
}
