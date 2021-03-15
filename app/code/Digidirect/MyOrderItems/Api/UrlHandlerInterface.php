<?php

namespace Digidirect\MyOrderItems\Api;

/**
 * Interface UrlHandlerInterface
 * @package Digidirect\MyOrderItems\Api
 */
interface UrlHandlerInterface
{
    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function process(\Magento\Framework\App\RequestInterface $request);
}
