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
        $fullAction = $this->request->getFullActionName();
        $this->logger->info('Algolia Observer fired: ' . $fullAction);

        if (strpos($fullAction, 'algolia_landingpage') !== false) {
            $layout = $observer->getData('layout');
            $path = trim($this->request->getPathInfo(), '/');
            $path = preg_replace('/(\.html$|\/$)/', '', $path);
            $slugHandle = preg_replace('/[^a-z0-9_]+/i', '_', strtolower($path));

            $handle = 'algolia_landingpage_view_' . $slugHandle;
            $this->logger->info('Adding handle: ' . $handle);

            $layout->getUpdate()->addHandle($handle);
        }
    }
}
