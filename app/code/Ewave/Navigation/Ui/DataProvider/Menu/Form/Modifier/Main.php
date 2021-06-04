<?php

namespace Ewave\Navigation\Ui\DataProvider\Menu\Form\Modifier;

use Ewave\Navigation\Model\Menu\CustomOptions;
use Ewave\Navigation\Model\Type;
use Ewave\Navigation\Model\TypeIdMapper;
use Magento\Framework\Registry;

class Main extends MenuModifier
{
    /**
     * @var CustomOptions
     */
    protected $customOptionsProcessor;

    /**
     * @var Type
     */
    protected $typeIdMapper;

    /**
     * Main constructor.
     * @param Registry $registry
     * @param CustomOptions $customOptions
     * @param TypeIdMapper $typeIdMapper
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        CustomOptions $customOptions,
        TypeIdMapper $typeIdMapper,
        array $data = []
    ) {
        $this->customOptionsProcessor = $customOptions;
        $this->typeIdMapper = $typeIdMapper;
        parent::__construct($registry, $data);
    }

    /**
     * Add categories drop down widget
     * Add "Use default" checkboxes if need
     * Modify buttons
     *
     * @param array $meta
     * @return []
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->_getModifiedMeta($meta);
        return $meta;
    }

    /**
     * Modify fields meta data, add "Use default" checkbox if can be added
     *
     * @param [] $meta
     * @return []
     */
    protected function _getModifiedMeta($meta)
    {
        if (!$this->_getCurrentMenuItem()->getId()) {
            return $meta;
        }
        $newMeta = [];
        foreach ($this->_getEditableInStoreFields() as $field) {
            if (!$this->_needToShowLabel($field)) {
                continue;
            }
            $newMeta[self::MENU_ITEM_INFORMATION_DATASCOPE]['children'][$field] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'service' => [
                                'template' => 'ui/form/element/helper/service',
                            ],
                            'disabled' => $this->_isDisabled($field),
                        ],
                    ],
                ],
            ];
        }

        return array_merge_recursive($meta, $newMeta);
    }

    /**
     * Modify data based on current and default store data
     *
     * @param [] $data
     * @return []
     */
    public function modifyData(array $data)
    {
        $currentMenuItem = $this->_getCurrentMenuItem();
        $defaultData = $currentMenuItem->getDataDefault();
        $storeData = $currentMenuItem->getData();

        $fields = $this->_getEditableInStoreFields();

        foreach ($data as $id => $itemData) {
            foreach ($fields as $field) {
                $data[$id][$field] = $storeData[$field] ?? $defaultData[$field] ?? $itemData[$field] ?? null;
            }
            $data[$id]['menu_store_id'] = $storeData['menu_store_id'] ?? [];
            $data[$id]['set_id'] = isset($storeData['set_id']) ? explode(',', $storeData['set_id']) : [];
            $customOptions = $storeData['custom_options'] ?? $defaultData['custom_options']
                ?? $itemData['custom_options'] ?? null;

            $unserialized = $this->customOptionsProcessor->unserialize($customOptions);
            if ($unserialized) {
                $data[$id]['custom_options_grid'] = $unserialized;
            }
        }

        $this->modifyTypeId($data);
        return $data;
    }

    /**
     * @param array $data
     * @return void
     */
    protected function modifyTypeId(&$data)
    {
        foreach ($data as $id => $itemData) {
            $data[$id]['type_id'] = $this->typeIdMapper->getExpectedIdByDbId($itemData['type_id'] ?? null);
        }
    }
}
