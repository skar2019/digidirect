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

        // Only for brand pages (assuming ?brands=apple in URL)
        $brand = $this->request->getParam('brands');
        if (!$brand) {
            return;
        }

        $layoutUpdate = $observer->getEvent()->getLayout()->getUpdate();
        $handle = 'algolia_brands_page';

        $this->logger->info('Adding Algolia brands page handle: ' . $handle);
        $layoutUpdate->addHandle($handle);
    }
}
