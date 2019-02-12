<?php
namespace Ewave\AbstractEntity\Model\ResourceModel\Eav;

use Magento\Eav\Model\ResourceModel\Entity\Attribute as EavAttribute;

class Attribute extends \Magento\Catalog\Model\ResourceModel\Eav\Attribute
{
    const MODULE_NAME = 'Ewave_AbstractEntity';

    const ENTITY = 'ewave_abstractentity_eav_attribute';

    const KEY_IS_GLOBAL = 'is_global';

    const KEY_USE_IN_INDEX_TABLE = 'use_in_index_table';

    const KEY_IS_USED_IN_GRID = 'is_used_in_grid';

    const KEY_IS_FILTERABLE_IN_GRID = 'is_filterable_in_grid';

    const USE_IN_INDEX_TABLE_ENABLE = 1;

    const USE_IN_INDEX_TABLE_DISABLE = 0;

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(EavAttribute::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceModel($model)
    {
        $model = $this->getData('source_model') ?: $model;
        return parent::setSourceModel($model);
    }
}
