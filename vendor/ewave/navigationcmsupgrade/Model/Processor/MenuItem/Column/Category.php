<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

/**
 * Class Category
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class Category extends AbstractProcessor implements FieldProcessorInterface
{
    /**
     * @param [] $data
     * @param string $key
     * @return mixed
     */
    public function getData(array $data, $key)
    {
        $connection = $this->menuItemResource->getConnection();
        $category = !empty($data[$key]) ? $data[$key] : null;
        if (!$category) {
            return null;
        }
        $select = $connection->select()
            ->from($this->menuItemResource->getTable('catalog_category_entity'), ['entity_id'])
            ->where($connection->quoteInto('entity_id = ?', $category));
        $category = $connection->fetchOne($select);
        return $category ?: null;
    }
}
