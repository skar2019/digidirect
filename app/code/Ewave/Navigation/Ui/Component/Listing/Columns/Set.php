<?php

namespace Ewave\Navigation\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Ewave\Navigation\Model\ResourceModel\Set\CollectionFactory as SetCollectionFactory;
use Ewave\Navigation\Model\ResourceModel\Set\Collection as SetCollection;

class Set extends Column
{
    /**
     * @var SetCollection
     */
    protected $collection;

    /**
     * Type constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SetCollectionFactory $collectionFactory
     * @param [] $components
     * @param [] $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SetCollectionFactory $collectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
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
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $sets = $item['menu_set_id'] ?? '';
        $menuSetId = explode(',', $sets);
        $itemName = [];
        foreach ($this->collection as $itemSet) {
            if (in_array($itemSet->getSetId(), $menuSetId)) {
                $itemName[] = $itemSet->getName();
            }
        }
        return implode(', ', $itemName);
    }
}
