<?php

namespace Digidirect\ProductFilter\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;
use Digidirect\ProductFilter\Model\CollectionManager;

/**
 * Class AddBackOrderColumnToCollection
 * @package Digidirect\Ui\DataProvider\Product
 */
class AddBackOrderFieldToCollection implements AddFieldToCollectionInterface
{
    /**
     * @var CollectionManager
     */
    private $collectionManager;

    /**
     * AddBackOrderFieldToCollection constructor.
     * @param CollectionManager $collectionManager
     */
    public function __construct(CollectionManager $collectionManager)
    {
        $this->collectionManager = $collectionManager;
    }

    /**
     * @param Collection $collection
     * @param string $field
     * @param null $alias
     * @return void
     */
    public function addField(Collection $collection, $field, $alias = null)
    {
        $this->collectionManager->preparedCollection($collection);
    }
}
