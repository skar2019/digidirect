<?php

namespace Digidirect\Navigation\Ui\DataProvider\Menu\Listing;

use Digidirect\Navigation\Model\ResourceModel\Menu\Grid\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 *
 * @method \Digidirect\Navigation\Model\ResourceModel\Menu\Collection getCollection()
 */
class MenuDataProvider extends AbstractDataProvider
{
    /**
     * Navigation collection
     *
     * @var \Digidirect\Navigation\Model\ResourceModel\Menu\Collection
     */
    protected $collection;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return void;
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == 'store_id') {
            $this->getCollection()->setCurrentStoreId($filter->getValue());
            $this->getCollection()->addStoreFilter();
        }
        parent::addFilter($filter);
    }
}
