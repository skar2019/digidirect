<?php
namespace Digidirect\Brands\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Forward;

class Router implements RouterInterface
{
    protected $actionFactory;

    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    public function match(RequestInterface $request)
    {
        $pathInfo = trim($request->getPathInfo(), '/');

        // Only handle /brands/* URLs
        if (preg_match('#^brands/([a-z0-9-]+)$#', $pathInfo, $matches)) {
            $brandSlug = $matches[1];

            // Forward to the view controller
            $request->setModuleName('brands')
                ->setControllerName('view')
                ->setActionName('index')
                ->setParam('brand', $brandSlug);

            return $this->actionFactory->create(Forward::class);
        }

        return false; // not our router
    }
}
