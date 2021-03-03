<?php

namespace Digidirect\Navigation\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Digidirect\Navigation\Model\ResourceModel\Menu\Grid\CollectionFactory as MenuCollectionFactory;
use Digidirect\Navigation\Model\ResourceModel\Menu\Grid\Collection as MenuCollection;

class Menu extends Column
{
    /**
     * @var MenuCollection
     */
    protected $_collection;

    /**
     * Type constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param MenuCollectionFactory $collectionFactory
     * @param [] $components
     * @param [] $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        MenuCollectionFactory $collectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_collection = $collectionFactory->create();
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string|null
     */
    protected function prepareItem(array $item)
    {
        foreach ($this->_collection as $itemSet) {
            if ($itemSet->getEntityId() == $item['parent_menu_item_id']) {
                return $itemSet->getTitle();
            }
        }
        return null;
    }
}
