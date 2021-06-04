<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic\Category;

use Ewave\Feed\Block\Adminhtml\Dynamic\AbstractGrid;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Ewave\Feed\Model\ResourceModel\Dynamic\Category\CollectionFactory;

class Grid extends AbstractGrid
{
    /**
     * @var string
     */
    protected $_idFieldIndex = 'mapping_id';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Grid constructor.
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
}
