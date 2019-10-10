<?php

namespace Ewave\ProductFilter\Ui\DataProvider\Product;

use Ewave\ProductFilter\Model\CollectionManager;
use Magento\CatalogInventory\Model\Configuration as CatalogInventoryConfiguration;
use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Class AddBackOrderColumnToCollection
 * @package Ewave\Ui\DataProvider\Product
 */
class AddBackOrderFilterToCollection implements AddFilterToCollectionInterface
{
    /**
     * @var CollectionManager
     */
    private $collectionManager;

    /**
     * AddBackOrderFieldToCollection constructor.
     * @param CollectionManager $collectionManager
     */
    public function __construct(
        CollectionManager $collectionManager
    ) {
        $this->collectionManager = $collectionManager;
    }

    /**
     * @param Collection $collection
     * @param string $field
     * @param null $condition
     * @return $this
     */
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        if (isset($condition['in'])) {
            $collection = $this->collectionManager->preparedCollection($collection);
            $this->collectionManager->addFilter($collection, $field, $condition);
        }
        return $this;
    }
}
