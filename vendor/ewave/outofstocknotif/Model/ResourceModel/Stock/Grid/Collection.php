<?php
namespace Ewave\OutOfStockNotif\Model\ResourceModel\Stock\Grid;

use Magento\ProductAlert\Model\ResourceModel\Stock\Collection as StockCollection;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Eav\Model\AttributeRepository;
use Magento\Store\Ui\Component\Listing\Column\Store;

/**
 * Class Collection
 * @package Ewave\OutOfStockNotif\Model\ResourceModel\Stock\Grid
 */
class Collection extends StockCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param AttributeRepository $attributeRepository
     * @param null $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param string $model
     * @param null $connection
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        AttributeRepository $attributeRepository,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection
        );

        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->_setIdFieldName('alert_stock_id');
        $attribute = $attributeRepository->get(Product::ENTITY, ProductInterface::NAME);

        $this->getSelect()->join(
            ['product' => $this->getTable('catalog_product_entity')],
            'main_table.product_id = product.entity_id',
            [ProductInterface::SKU]
        );

        $this->getSelect()->join(
            ['product_varchar' => $this->getTable('catalog_product_entity_varchar')],
            'product.row_id = product_varchar.row_id AND product_varchar.store_id = 0',
            ['product_name' => 'value']
        )->where($this->getResource()->getConnection()->quoteInto(
            'product_varchar.attribute_id = ?',
            $attribute->getAttributeId()
        ));

        $this->getSelect()->joinLeft(
            ['customer' => $this->getTable('customer_entity')],
            'main_table.customer_id=customer.entity_id',
            [
                'email' => new \Zend_Db_Expr('IF(LENGTH(main_table.email) > 0, main_table.email, customer.email)'),
                'firstname' => new \Zend_Db_Expr('IF(customer.entity_id > 0, customer.firstname, "[Guest]")'),
                'lastname' => new \Zend_Db_Expr('IF(customer.entity_id > 0, customer.lastname, "[Guest]")'),
            ]
        );

        $this->addFilterToMap('email', 'main_table.email');
        $this->addFilterToMap('product_name', 'product_varchar.value');
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'email') {
            return parent::addFieldToFilter(['main_table.email', 'customer.email'], [$condition, $condition]);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
