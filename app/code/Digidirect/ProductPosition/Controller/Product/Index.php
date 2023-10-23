<?php

namespace Digidirect\ProductPosition\Controller\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
     /**
     * @var StoreManagerInterface
     */
    
    protected $storeManager;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CategoryResource
     */
    protected $categoryResource;
    
    
    protected $_productFactory;
    
    
    protected $logger;
    

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ProductCollectionFactory $productCollectionFactory,  
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        CategoryResource $categoryResource,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        $this->logger = $logger;
        $this->_productFactory = $productFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $product = $this->_productFactory->create()->load(3793);
        // Get the category ID of the new product.
        $categoryIds = $product->getCategoryIds();
        //$this->$logger->info($product->getCategoryIds());
        // Get the new product position.
        $newPosition = 5;
        // Change the product position.
        $this->logger->info("Product ID: " . $product->getId());
        $this->logger->info("Category IDs: " . $categoryIds);
        $this->changeProductPosition($categoryIds, $product->getId(), $newPosition);
    }
    
    private function changeProductPosition($categoryIds, $productId, $newPosition)
    {
        foreach($categoryIds as $categoryId)
        {
             $category = $this->categoryFactory->create()->load($categoryId);
             $products = $category->getProductsPosition();
             $products[$productId] = $newPosition;
             $category->setPostedProducts($products);
             $category->save();
        }
    }
}