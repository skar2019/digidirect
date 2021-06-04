<?php

namespace Ewave\PreOrder\Model\Preorder;

/**
 * Class PreorderModel
 *
 * @package Ewave\PreOrder\Model
 */
class PreorderModel extends \Magento\Framework\DataObject implements \Ewave\PreOrder\Api\Data\PreorderModelInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->_getData(self::MODEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model)
    {
        $this->setData(self::MODEL, $model);
        return $this;
    }
}
