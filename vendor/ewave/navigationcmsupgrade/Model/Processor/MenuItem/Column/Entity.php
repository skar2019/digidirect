<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

use Magento\Cms\Model\BlockFactory;

/**
 * Class Entity
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class Entity extends AbstractProcessor implements FieldProcessorInterface
{
    /**
     * @param [] $data
     * @param string $key
     * @return mixed
     */
    public function getData(array $data, $key)
    {
        return $data[$key] ?? null;
    }

    /**
     * @param array $data
     * @return null|string
     */
    public function getEntityIdByCode(array $data)
    {
        $menuItemCode = $this->getField($data, 'menu_item_code');
        if (!$menuItemCode) {
            return null;
        }

        $select = $this->menuItemResource->getConnection()->select()
            ->from('ewave_navigation_menu_entity', 'entity_id')
            ->where($this->menuItemResource->getConnection()->quoteInto('menu_item_code = ?', $menuItemCode));
        $result = $this->menuItemResource->getConnection()->fetchOne($select);
        return $result ?: null;
    }
}
