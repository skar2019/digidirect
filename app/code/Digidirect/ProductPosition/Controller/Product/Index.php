<?php

namespace Digidirect\ProductPosition\Controller\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Digidirect\ProductPosition\Helper\ReadCsv;

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
    
    
    protected $helper;
    

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ProductCollectionFactory $productCollectionFactory,  
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        CategoryResource $categoryResource,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        ReadCsv $helper
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        $this->logger = $logger;
        $this->_productFactory = $productFactory;
        $this->helper = $helper;
        return parent::__construct($context);
    }

    public function execute()
    {
        //Set default position
        $categoryId = 65; //Special Effects Lens
        $category = $this->categoryFactory->create()->load($categoryId);
        $products = $category->getProductsPosition();
        foreach ($products as $id=>$value){
            $products[$id] = 0;
        }
        foreach ($products as $id=>$value){
            $products[$id] = 50;
        }
        $category->setPostedProducts($products);
        $category->save();
        
        $this->helper->readCsv('product_position.csv');
    }
}