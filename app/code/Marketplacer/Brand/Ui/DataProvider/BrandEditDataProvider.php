<?php

namespace Marketplacer\Brand\Ui\DataProvider;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\Brand\Model\ResourceModel\Brand\Grid\CollectionFactory;

class BrandEditDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->request->getParam('store', Store::DEFAULT_STORE_ID);
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        $items = $this->getCollection()->getItems();
        /** @var Brand $brand */
        foreach ($items as $brand) {
            $brand->setStoreId($this->getStoreId());
            $this->loadedData[$brand->getBrandId()] = $brand->getData();
        }
        return $this->loadedData;
    }

    /**
     * @return AbstractCollection
     */
    public function getCollection()
    {
        if ($this->collection === null) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->addStoreIdToFilter($this->getStoreId(), true);
        }
        return $this->collection;
    }
}
