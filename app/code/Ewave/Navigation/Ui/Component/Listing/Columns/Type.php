<?php

namespace Ewave\Navigation\Ui\Component\Listing\Columns;

use Ewave\Navigation\Model\NotFilteredTypes;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Ewave\Navigation\Model\ResourceModel\Type\Collection as TypeCollection;

class Type extends Column
{
    /**
     * @var TypeCollection
     */
    protected $collection;

    /**
     * Type constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param NotFilteredTypes $collectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        NotFilteredTypes $collectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->getCollection();
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
        foreach ($this->collection as $itemType) {
            if ($itemType->getTypeId() == $item['type_id']) {
                return $itemType->getTypeName();
            }
        }
        return '';
    }
}
