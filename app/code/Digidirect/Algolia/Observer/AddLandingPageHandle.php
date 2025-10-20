<?php
namespace Digidirect\Algolia\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;

class AddLandingPageHandle implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;
    protected $logger;

    public function __construct(
        RequestInterface $request,
        \Psr\Log\LoggerInterface $logger,
    ){
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Add dynamic layout handle for specific Algolia Merchandising Landing Pages.
     */
    public function execute(Observer $observer)
    {
        // Check if this is an Algolia Merchandising landing page
        if ($this->request->getFullActionName() === 'algolia_landingpage_view') {
            $layout = $observer->getData('layout');

            // Example: "/deals" or "/brands/canon"
            $path = trim($this->request->getPathInfo(), '/');

            // Remove .html or trailing slashes, make handle safe
            $path = preg_replace('/(\.html$|\/$)/', '', $path);
            $slugHandle = preg_replace('/[^a-z0-9_]+/i', '_', strtolower($path));
            $this->logger->info('handle, ' . 'algolia_landingpage_view_' . $slugHandle);
            // Example handle: algolia_landingpage_view_deals
            $layout->getUpdate()->addHandle('algolia_landingpage_view_' . $slugHandle);
        }
    }
}
