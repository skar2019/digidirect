<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic;

use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Backend\Helper\Data as BackendHelper;

class AbstractGrid extends GridExtended
{
    /**
     * @var string
     */
    protected $_idFieldIndex = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header' => __('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => $this->_idFieldIndex,
        ]);

        $this->addColumn('name', [
            'header' => __('Name'),
            'align' => 'left',
            'index' => 'name',
        ]);

        $this->addColumn('code', [
            'header' => __('Code'),
            'align' => 'left',
            'index' => 'code',
        ]);

        $this->addColumn('action', [
            'header' => __('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('Edit'),
                    'url' => ['base' => '*/*/edit'],
                    'field' => 'id',
                ],
                [
                    'caption' => __('Delete'),
                    'url' => ['base' => '*/*/delete'],
                    'field' => 'id',
                ],
            ],
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
