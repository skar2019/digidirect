<?php
namespace Ewave\Faq\Ui\DataProvider\Category\Form;

use Ewave\Faq\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Ewave\Faq\Model\Category as Category;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class CategoryDataProvider
 * @package Ewave\Faq\Ui\DataProvider\Category\Form
 */
class CategoryDataProvider extends AbstractDataProvider
{
    /**
     * CategoryDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CategoryCollectionFactory $categoryCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $categoryCollectionFactory->create();
    }

    /**
     * Used to be able modify data
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->collection->getItems();
        /**
         * @var Category $categoryItem
         */
        foreach ($items as $categoryItem) {
            $result = $categoryItem->getData();
            $this->data[$categoryItem->getId()] = $result;
        }
        return $this->data;
    }
}
