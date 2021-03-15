<?php

namespace Digidirect\MyOrderItems\Model;

use Magento\Framework\App\RequestInterface;
use Digidirect\MyOrderItems\Api\UrlHandlerInterface;

/**
 * Class UrlHandlerPool
 * @package Digidirect\Outlet\Model
 */
class UrlHandlerPool
{
    /**
     * @var UrlHandlerInterface[]
     */
    protected $handlers;

    /**
     * @param UrlHandlerInterface[] $handlers
     */
    public function __construct(
        $handlers = []
    ) {
        $this->handlers = $handlers;
    }

    /**
     * @param RequestInterface $request
     * @param array $exclude
     * @return array
     */
    public function execute(RequestInterface $request, $exclude = [])
    {
        $urlParams = [];
        if (!empty($this->handlers)) {
            /** @var UrlHandlerInterface $handler */
            foreach ($this->handlers as $handler) {
                if (!$handler instanceof UrlHandlerInterface) {
                    throw new \InvalidArgumentException(__(
                        'Type %1 is not an instance of %2',
                        get_class($handler),
                        UrlHandlerInterface::class
                    ));
                }
                $handlerResult = $handler->process($request);
                if (!empty($handlerResult)) {
                    $urlParams = array_merge_recursive($urlParams, $handlerResult);
                }
            }
            if (!empty($exclude)) {
                $urlParams = $this->filter($exclude, $urlParams);
            }
            return $urlParams;
        }
        return [];
    }

    /**
     * @param array $unsetParams
     * @param array $urlParams
     * @return array
     */
    protected function filter(array $unsetParams, array $urlParams)
    {
        if (!empty($unsetParams)) {
            foreach ($unsetParams as $unsetParam) {
                if (in_array($unsetParam, array_keys($urlParams))) {
                    unset($urlParams[$unsetParam]);
                }
                if (isset($urlParams['_query'])
                    && in_array($unsetParam, array_keys($urlParams['_query']))) {
                    unset($urlParams['_query'][$unsetParam]);
                }
            }
            if (isset($urlParams['_query']) && empty($urlParams['_query'])) {
                unset($urlParams['_query']);
            }
        }
        return $urlParams;
    }
}
