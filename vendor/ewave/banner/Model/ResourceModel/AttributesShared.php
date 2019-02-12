<?php

namespace Ewave\Banner\Model\ResourceModel;

use Magento\Framework\ObjectManagerInterface;

/**
 * @since 2.0.4
 */
class AttributesShared
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var null|Attributes
     */
    protected $resourceModel = null;

    /**
     * @var string
     */
    protected $instance;

    /**
     * AttributesShared constructor.
     * @param ObjectManagerInterface $objectManager
     * @param string $instance
     */
    public function __construct(ObjectManagerInterface $objectManager, $instance = Attributes::class)
    {
        $this->objectManager = $objectManager;
        $this->instance = $instance;
    }

    /**
     * @return Attributes|mixed|null
     */
    public function get()
    {
        if (null === $this->resourceModel || !($this->resourceModel instanceof $this->instance)) {
            $this->resourceModel = $this->objectManager->get(Attributes::class);
        }
        return $this->resourceModel;
    }
}
