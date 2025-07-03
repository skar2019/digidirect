<?php

namespace Digidirect\PageCache\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Cache\Manager as CacheManager;

class FlushPageCache implements ObserverInterface
{
    protected CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $observer->getEvent()->getObject();

        if ($page->getIdentifier() === 'home') {
            $this->cacheManager->clean(['full_page']);
        }
    }
}
