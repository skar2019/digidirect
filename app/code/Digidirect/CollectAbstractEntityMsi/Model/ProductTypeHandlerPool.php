<?php

namespace Digidirect\CollectAbstractEntityMSI\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Digidirect\CollectAbstractEntityMSI\Api\ProductTypeHandlerInterface;
use Digidirect\CollectAbstractEntityMSI\Model\ProductTypeHandler\AbstractHandler;

/**
 * Class ProductTypeHandlerPool
 * @package Digidirect\CollectAbstractEntityMSI\Model
 */
class ProductTypeHandlerPool
{
    /**
     * @var ProductTypeHandlerInterface[]
     */
    protected $handlers;

    /**
     * @param ProductTypeHandlerInterface[] $handlers
     */
    public function __construct(
        $handlers = []
    ) {
        $this->handlers = $handlers;
    }

    /**
     * @param ProductInterface $product
     * @param array $options
     * @return array
     */
    public function execute(ProductInterface $product, $options = [])
    {
        $skus = [];
        if (!empty($this->handlers)) {
            /** @var ProductTypeHandlerInterface $handler */
            foreach ($this->handlers as $handler) {
                if (!$handler instanceof AbstractHandler) {
                    throw new \InvalidArgumentException(__(
                        'Type %1 is not an instance of %2',
                        get_class($handler),
                        AbstractHandler::class
                    ));
                }
                if ($product->getTypeId() == $handler->getTypeId()) {
                    $skus = $handler->process($product, $options);
                }
            }
        }
        return $skus;
    }
}
