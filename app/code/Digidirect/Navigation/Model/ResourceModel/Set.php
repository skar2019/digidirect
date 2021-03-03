<?php

namespace Digidirect\Navigation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Set
 * @package Digidirect\Navigation\Model\ResourceModel
 */
class Set extends AbstractDb
{
    /**
     * Set table and primary key column name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digidirect_navigation_menu_set', 'set_id');
    }

    /**
     * Save stores relaction
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_saveStoreSet($object);
        parent::_afterSave($object);
        return $this;
    }

    /**
     * Save menu set store association
     *
     * @param \Digidirect\Navigation\Model\Set $object
     * @return $this
     */
    protected function _saveStoreSet($object)
    {
        if ($object->getId()) {
            $setsIdsArray = $object->getStoreId();

            $connection = $this->getConnection();

            $setStoreTable = $this->getTable('digidirect_navigation_menu_set_store');

            $select = $connection->select()
                ->from($setStoreTable, ['store_id'])
                ->where($connection->quoteInto('menu_set_id = ?', $object->getId()));

            $oldSetIds = $connection->fetchCol($select, ['store_id']);

            $insert = array_diff($setsIdsArray, $oldSetIds);
            $delete = array_diff($oldSetIds, $setsIdsArray);

            if (!empty($delete)) {
                foreach ($delete as $storeId) {
                    $condition = ['menu_set_id = ?' => (int)$object->getId(), 'store_id = ?' => (int)$storeId];
                    $connection->delete($setStoreTable, $condition);
                }
            }

            if (!empty($insert)) {
                $ids = [];
                foreach ($setsIdsArray as $setId) {
                    $ids[] = ['store_id' => $setId, 'menu_set_id' => $object->getId()];
                }

                $this->getConnection()->insertOnDuplicate(
                    $this->getTable('digidirect_navigation_menu_set_store'),
                    $ids
                );
            }
        }

        return $this;
    }

    /**
     * Add stores ids
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $object->setStoreIds($this->getStoreIds($object->getId()));
        }
        parent::_afterLoad($object);
        return $this;
    }

    /**
     * Get store ids by set
     *
     * @param int $id
     * @return []
     */
    public function getStoreIds($id)
    {
        $select = $this->getConnection()->select()
            ->from('digidirect_navigation_menu_set_store', ['store_id'])
            ->where($this->getConnection()->quoteInto('menu_set_id =?', $id));
        return $this->getConnection()->fetchCol($select, ['store_id']);
    }
}
