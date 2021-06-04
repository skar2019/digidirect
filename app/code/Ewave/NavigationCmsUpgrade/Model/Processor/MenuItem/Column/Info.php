<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

/**
 * Class Info
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class Info extends AbstractProcessor implements FieldProcessorInterface
{
    /**
     * @param array $data
     * @param string $key
     * @return mixed|null
     */
    public function getData(array $data, $key)
    {
        return $data[$key] ?? null;
    }

    /**
     * @param [] $itemData
     * @return int
     */
    public function getParentMenuItemId($itemData)
    {
        $connection = $this->menuItemResource->getConnection();

        $select = $connection->select()
            ->from('ewave_navigation_menu_entity', ['entity_id'])
            ->where($connection->quoteInto('menu_item_code = ?', $itemData['parent_menu_item_code']));
        $result = $connection->fetchOne($select);
        return $result ?:0;
    }

    /**
     * @param [] $itemData
     * @return array
     */
    public function getStores($itemData)
    {
        return [$itemData['store_id']];
    }
}
