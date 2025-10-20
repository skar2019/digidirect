<?php
namespace Digidirect\Algolia\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use Algolia\AlgoliaSearch\Model\LandingPageFactory;

class AddLandingPageHandle implements ObserverInterface
{
    protected $request;
    protected $logger;
    protected $landingPageFactory;

    public function __construct(
        RequestInterface $request,
        LoggerInterface $logger,
        LandingPageFactory $landingPageFactory
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->landingPageFactory = $landingPageFactory;
    }

    public function execute(Observer $observer)
    {
        $fullAction = $this->request->getFullActionName();
        if ($fullAction !== 'algolia_landingpage_view') {
            return;
        }

        $layout = $observer->getData('layout');
        $landingPageId = (int) $this->request->getParam('landing_page_id');

        if ($landingPageId) {
            try {
                $landingPage = $this->landingPageFactory->create()->load($landingPageId);
                $slug = $landingPage->getUrlKey() ?: 'landing_page_' . $landingPageId;
                $handle = 'algolia_landingpage_view_' . preg_replace('/[^a-z0-9_]+/i', '_', strtolower($slug));

                $this->logger->info('Adding handle: ' . $handle);
                $layout->getUpdate()->addHandle($handle);
            } catch (\Exception $e) {
                $this->logger->error('Failed to add Algolia handle: ' . $e->getMessage());
            }
        }
    }
}
