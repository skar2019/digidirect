<?php

namespace Digidirect\Navigation\Model;

use Digidirect\Navigation\Api\Data\TypeInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\Collection\AbstractDb;
use Digidirect\Navigation\Model\ResourceModel\Type as TypeResourceModel;

class Type extends AbstractModel implements TypeInterface
{
    /**
     * @var array
     */
    protected $expectedTypes = [];

    /**
     * Type constructor.
     * @param Context $context
     * @param Registry $registry
     * @param TypeResourceModel $resource
     * @param array $typesConfiguration
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        TypeResourceModel $resource,
        array $typesConfiguration,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->expectedTypes = $typesConfiguration;
    }

    /**
     * {@inheritdoc}
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Digidirect\Navigation\Model\ResourceModel\Type');
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->expectedTypes;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setMenuTypeCode($code)
    {
        $this->setData(self::TYPE_CODE);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMenuTypeCode()
    {
        return $this->getData(self::TYPE_CODE);
    }

    /**
     * @param string $typeName
     * @return $this
     */
    public function setTypeName($typeName)
    {
        $this->setData(self::TYPE_NAME);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeName()
    {
        return $this->getData(self::TYPE_NAME);
    }
}
