<?php
namespace Digidirect\Algolia\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class AddLandingPageHandle implements ObserverInterface
{
    protected $request;
    protected $logger;

    public function __construct(
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        // Only for Algolia landing pages
        if ($this->request->getFullActionName() !== 'algolia_landingpage_view') {
            return;
        }

        $layout = $observer->getData('layout');
        $landingPageId = (int) $this->request->getParam('landing_page_id');
        
        // ✅ Get the original, friendly URL path (not internal)
        $originalPath = trim($this->request->getOriginalPathInfo(), '/'); // e.g. "brands/apple"

        $this->logger->info('Original path: ' . $originalPath);

        // ✅ Check if it starts with "brands/"
        if (strpos($originalPath, 'brands/') === 0) {
            $handle = 'algolia_landingpage_default_brands_banner';
            $layout->getUpdate()->addHandle($handle);
        } else {
            if ($landingPageId) {
                // Add specific landing page handle
                $handle = 'algolia_landingpage_view_landing_page_id_' . $landingPageId;
                $layout->getUpdate()->addHandle($handle);
            } 
        }
    }
}
