<?php

namespace Ewave\Navigation\Ui\DataProvider\Set\Form;

use Ewave\Navigation\Model\ResourceModel\Set\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class SetDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Ewave\Navigation\Model\ResourceModel\Set\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $setCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $setCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $setCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Ewave\Navigation\Model\Set $set */
        foreach ($items as $set) {
            $this->loadedData[$set->getId()] = $set->getData();
        }

        $data = $this->dataPersistor->get('current_navigation_set');
        if (!empty($data)) {
            $set = $this->collection->getNewEmptyItem();
            $set->setData($data);
            $this->loadedData[$set->getId()] = $set->getData();
            $this->dataPersistor->clear('current_navigation_set');
        }

        return $this->loadedData;
    }
}
