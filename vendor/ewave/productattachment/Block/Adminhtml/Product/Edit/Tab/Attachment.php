<?php
namespace Ewave\ProductAttachment\Block\Adminhtml\Product\Edit\Tab;

use Ewave\ProductAttachment\Block\Adminhtml\Product\ProductTrait;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Ewave\ProductAttachment\Model\ResourceModel\Attachment\CollectionFactory;
use Ewave\ProductAttachment\Api\AttachmentRepositoryInterface;
use Ewave\ProductAttachment\Model\Attachment as AttachmentModel;

/**
 * Class Attachment
 * @package Ewave\ProductAttachment\Block\Adminhtml\Product\Edit
 */
class Attachment extends \Magento\Backend\Block\Widget\Grid\Extended
{
    use ProductTrait;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Attachment constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        AttachmentRepositoryInterface $attachmentRepository,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->registry = $registry;
        $this->attachmentRepository = $attachmentRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_product_attachments');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('product');
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            $this->getCollection()->addColumnFilterToCollection(
                $this->getProductId(),
                $this->getStoreId(),
                $column->getFilter()->getValue()
            );
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        /** @var \Ewave\ProductAttachment\Model\ResourceModel\Attachment\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->setProductId($this->getProductId());
        $collection->joinAttributes($this->getStoreId());
        $collection->addPositionToSelect($this->getProductId(), $this->getStoreId());
        $collection->groupByEntityId();
        $collection->orderByPosition();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
                'type' => 'checkbox',
                'name' => 'in_product',
                'values' => $this->getSelectedAttachments(),
                'index' => 'entity_id',
                'sortable' => false,
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Attachment Name'),
                'index' => 'name',
                'sortable' => false,
                'filter_condition_callback' => [$this, 'filterNameCondition']

            ]
        );

        $this->addColumn(
            AttachmentModel::STATUS,
            [
                'header' => __('Status'),
                'index' => AttachmentModel::STATUS,
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Ewave\ProductAttachment\Block\Adminhtml\Product\Edit\Tab\Renderer\Status'
            ]
        );

        $this->addColumn(
            'store_id',
            [
                'header' => __('Scope'),
                'index' => 'store_id',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
                'filter' => false
            ]
        );

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'filter' => false,
                'sortable' => false,
                'editable' => true,
                'edit_only' => true
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('ewave_product_attachment/index/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function getSelectedAttachments()
    {
        return $this->attachmentRepository->getRelationIds($this->getProductId(), $this->getStoreId());
    }

    /**
     * @param \Ewave\ProductAttachment\Model\ResourceModel\Attachment\Collection $collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column\Extended $column
     * @return $this
     */
    protected function filterNameCondition($collection, $column)
    {
        $collection->addFilterByAttribute($column->getIndex(), $column->getFilter()->getCondition());
        return $this;
    }
}
