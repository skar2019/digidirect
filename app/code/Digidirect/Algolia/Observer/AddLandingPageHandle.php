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
        // Only for Algolia landing page view
        if ($this->request->getFullActionName() !== 'algolia_landingpage_view') {
            return;
        }

        $layout = $observer->getData('layout');
        $landingPageId = (int) $this->request->getParam('landing_page_id');
        
        $pathInfo = trim($this->request->getPathInfo(), '/');
        $this->logger->info('$pathInfo: ' . $pathInfo);
        
        //if (strpos($pathInfo, 'brands/') !== 0) {
        //    return;
        //}
        
        if ($landingPageId) {
            // Add specific landing page handle
            $handle = 'algolia_landingpage_view_landing_page_id_' . $landingPageId;
            $this->logger->info('Adding handle: ' . $handle);
            $layout->getUpdate()->addHandle($handle);
        } /*else {
            // Only apply default banner if "brand" is in the URL query parameter
            $brand = $this->request->getParam('brands'); // adjust param name if different
            if ($brand) {
                $defaultHandle = 'algolia_landingpage_default_brands_banner';
                $this->logger->info('Adding default handle for brand: ' . $brand);
                $layout->getUpdate()->addHandle($defaultHandle);
            }
        }*/
    }
}
