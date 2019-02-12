<?php
namespace Ewave\Navigation\Ui\DataProvider\Menu\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Category as CategoryModel;
use Ewave\Navigation\Model\Registry\Constants;

abstract class MenuModifier implements ModifierInterface
{
    const META_CONFIG_PATH = '/arguments/data/config';
    const MENU_ITEM_INFORMATION_DATASCOPE = 'menu_item_information';

    /**
     * @var \Ewave\Navigation\Model\Menu
     */
    protected $currentMenuItem;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var []
     */
    protected $data;

    /**
     * @var null|int
     */
    protected $storeId = null;

    /**
     * MenuModifier constructor.
     * @param Registry $registry
     * @param [] $data
     */
    public function __construct(
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->data = $data;
    }

    /**
     * @return int
     */
    protected function _getCurrentStoreId()
    {
        if (null === $this->storeId) {
            $this->storeId = $this->registry->registry(Constants::CURRENT_STORE_ID);
        }
        return (int)$this->storeId;
    }

    /**
     * Check if field is disabled
     *
     * @param string $fieldName
     * @return bool
     */
    protected function _isDisabled($fieldName)
    {
        if ($this->_isDefaultStore()) {
            return false;
        }

        $currentMenuItem = $this->_getCurrentMenuItem();
        if (!$id = $currentMenuItem->getId()) {
            return false;
        }
        $dataDefault = $currentMenuItem->getDataDefault();
        $storeData = $currentMenuItem->getData();
        return isset($dataDefault[$fieldName]) && empty($storeData[$fieldName]);
    }

    /**
     * @return bool
     */
    protected function _isDefaultStore()
    {
        return $this->_getCurrentStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }

    /**
     * Check if editable item in store view scope and field may be configured by store
     *
     * @param string $fieldName
     * @return bool
     */
    protected function _needToShowLabel($fieldName)
    {
        return $this->_getCurrentMenuItem()->getCurrentStoreId() != \Magento\Store\Model\Store::DEFAULT_STORE_ID
        && in_array($fieldName, $this->_getEditableInStoreFields());
    }

    /**
     * Get current menu Item
     *
     * @return \Ewave\Navigation\Model\Menu
     */
    protected function _getCurrentMenuItem()
    {
        if (!$this->currentMenuItem) {
            $this->currentMenuItem = $this->registry->registry(Constants::CURRENT_MENU_ITEM);
        }
        return $this->currentMenuItem;
    }

    /**
     * Get fields which require "Use default" in store view
     *
     * @return []
     */
    protected function _getEditableInStoreFields()
    {
        return $this->data['fields'] ?? [];
    }
}
