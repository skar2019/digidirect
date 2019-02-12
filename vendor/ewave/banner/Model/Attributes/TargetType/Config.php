<?php

namespace Ewave\Banner\Model\Attributes\TargetType;

class Config implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var []
     */
    protected $config;

    /**
     * TargetType constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @return []
     */
    public function toOptionArray()
    {
        $types = [['value' => 0, 'label' =>__('-- Please Select --')]];
        foreach ($this->config['types'] as $typeId => $config) {
            $types[] = ['value' => $typeId, 'label' => __($config['label'])];
        }
        return $types;
    }

    /**
     * @return []
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param int $targetTypeId
     * @return TargetTypeInterface
     */
    public function getTargetTypeObject($targetTypeId)
    {
        return isset($this->config['types'][$targetTypeId]['object'])
            ? $this->config['types'][$targetTypeId]['object']
            : null;
    }
}
