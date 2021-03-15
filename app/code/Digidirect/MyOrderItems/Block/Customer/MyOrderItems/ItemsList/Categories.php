<?php

namespace Digidirect\MyOrderItems\Block\Customer\MyOrderItems\ItemsList;

use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItems\Helper\Data as DataHelper;
use Digidirect\MyOrderItems\Model\OrderItemStateRepository;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as ItemCollection;
use Digidirect\MyOrderItems\Model\ResourceModel\Category\CollectionFactory;
use Digidirect\MyOrderItems\Block\Customer\MyOrderItems\AbstractBlock;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;

/**
 * Class Categories
 * @package Digidirect\MyOrderItems\Block\Customer\MyOrderItems\ItemsList
 */
class Categories extends AbstractBlock
{
    /**
     * Const category name
     */
    const NAME = 'name';

    /**
     * @var array
     */
    protected $categories = [];

    /**
     * @var string
     */
    protected $categoryName = '';

    /**
     * @var OrderItemStateRepository
     */
    protected $stateRepository;

    /**
     * @var CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Categories constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param CollectionFactory $categoryCollectionFactory
     * @param OrderItemStateRepository $stateRepository
     * @param UrlHandlerPool $urlHandlerPool
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        CollectionFactory $categoryCollectionFactory,
        OrderItemStateRepository $stateRepository,
        UrlHandlerPool $urlHandlerPool,
        array $data = []
    ) {
        $this->stateRepository = $stateRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct(
            $context,
            $dataHelper,
            $configHelper,
            $urlHandlerPool,
            $data
        );
    }

    /**
     * @param ItemCollection $collection
     */
    public function setCollection(ItemCollection $collection)
    {
        $categories = $this->getCategoryCollection(clone $collection);
        if ($categoryId = $this->getCurrentCategory()) {
            $this->stateRepository->filterCategories($collection, $categoryId);
        }
        foreach ($categories as $category) {
            $this->categories[$category->getEntityId()] = $category->getName();
        }
        if ($categoryId && isset($this->categories[$categoryId])) {
            $this->categoryName = $this->categories[$categoryId];
        }
    }

    /**
     * @param ItemCollection $collection
     * @return \Digidirect\MyOrderItems\Model\ResourceModel\Category\Collection
     */
    protected function getCategoryCollection(ItemCollection $collection)
    {
        /** @var \Digidirect\MyOrderItems\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = $this->categoryCollectionFactory->create();
        try {
            $categoryCollection->joinSaleItems(clone $collection);
            $categoryCollection->addAttributeToSelect(self::NAME);
        } catch (\Exception $exception) {
            return $categoryCollection;
        }
        return $categoryCollection;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @return int
     */
    public function getCurrentCategory()
    {
        return (int)$this->getRequest()->getParam($this->getCategoryVarName());
    }

    /**
     * @return string
     */
    public function getCategoryVarName()
    {
        return self::CATEGORY_PARAMETER;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }
}