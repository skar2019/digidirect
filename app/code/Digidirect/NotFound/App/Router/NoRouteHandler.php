<?php
namespace Digidirect\NotFound\App\Router;

class NoRouteHandler implements \Magento\Framework\App\Router\NoRouteHandlerInterface
{
    public function process(\Magento\Framework\App\RequestInterface $request)
    {
        $requestValue = ltrim($request->getPathInfo(), '/');
        $request->setParam('q', $requestValue);
        $request->setModuleName('catalogsearch')->setControllerName('result')->setActionName('index');
        return true;
    }
    //to redeploy
    //cant commit?
}