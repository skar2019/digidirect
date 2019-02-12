<?php
namespace Ewave\AdvancedInventory\Model\ResourceModel\AbstractEntity\Relation\Stock;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Magento\Framework\DataObject;

/**
 * Class ReadHandler
 */
class ReadHandler extends AbstractHandler
{
    /**
     * @param AbstractEntityInterface|DataObject $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        //var_dump($entity->getData()); die;
        return $entity;
    }
}
