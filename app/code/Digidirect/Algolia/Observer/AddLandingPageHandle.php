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
        if ($this->request->getFullActionName() !== 'algolia_landingpage_view') {
            return;
        }

        $layout = $observer->getData('layout');
        $landingPageId = (int) $this->request->getParam('landing_page_id');

        if ($landingPageId) {
            $handle = 'algolia_landingpage_view_landing_page_id_' . $landingPageId;
            $this->logger->info('Adding handle: ' . $handle);
            $layout->getUpdate()->addHandle($handle);
        }
    }
}
