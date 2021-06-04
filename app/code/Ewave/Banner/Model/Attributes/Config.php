<?php
namespace Ewave\Banner\Model\Attributes;

class Config
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
        $types = [];
        foreach ($this->config['types'] as $typeId => $config) {
            $types[$typeId] = $config['label'];
        }

        return $types;
    }

    /**
     * @param string $attributeCode
     * @return AttributesInterface
     */
    public function getTargetTypeObject($attributeCode)
    {
        return isset($this->config['types'][$attributeCode]) ? $this->config['types'][$attributeCode]: null;
    }
}
