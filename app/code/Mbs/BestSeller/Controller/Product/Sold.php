<?php

namespace Mbs\BestSeller\Controller\Product;

class Sold extends \Magento\Framework\App\Action\Action
{
    /*Product collection variable*/ 
    protected $_productCollection;

    protected $stockFilter;
    
    protected $logger;
    
    protected $resourceConfigurable;
    
    protected $_reportCollectionFactory;
    
    protected $productRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $resourceConfigurable,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\Reports\Model\ResourceModel\Product\Sold\CollectionFactory $reportCollectionFactory
    )
    {
        $this->logger = $logger;
        $this->_productCollection= $productCollection;
        $this->stockFilter = $stockFilter;  
        $this->resourceProduct = $resourceProduct;
        $this->resourceConfigurable = $resourceConfigurable;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->_reportCollectionFactory = $reportCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $productCollection = $this->getProductCollections();
        foreach ($productCollection as $product) {
            $setQty = "";
            if(isset($_GET["reset"])){
                $setQty = "";
            } else {
                $setQty = $this->getSoldQtyByProductId($product->getData('entity_id'));
            }
            try {
                $prod = $this->productRepository->get($product->getData('sku'));
                $prod->setCustomAttribute('nb_sales', $setQty);
                $this->productRepository->save($prod);
            }catch(Exception $e) {
                $this->logger->info('Message: ' . $e->getMessage());
            }
        }
        print_r('Done!');
    }
    
    public function getSoldQtyByProductId($productID = null) {
        $SoldProducts = $this->_reportCollectionFactory->create();
        $SoldProdudctCOl = $SoldProducts->addOrderedQty()->addAttributeToFilter('product_id', $productID);
        /* If does have any product id 
         * then return false
         */
        if(!$SoldProdudctCOl->count()):
            return false;
        endif;
        $SoldProdudctCOl->getSelect()->__toString();
        $product = $SoldProdudctCOl->getFirstItem();
        return (int)$product->getData('ordered_qty');
    }
    
    public function getProductCollections() {
        $cat = "";
        if(isset($_GET["cat"])){
            $cat = $_GET["cat"];
        }
        $this->logger->info('$cat: ' . $cat);
        $collection = $this->_productCollection->create();
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        if(isset($_GET["cat"])){
            $collection->addCategoriesFilter(['in' => $cat]);
        }
        
        if(isset($_GET["reset"])){
            $collection->addAttributeToFilter("nb_sales", array("null" => false));
        }
        
        //$collection->addAttributeToFilter("nb_sales", array("neq" => 0));
        //$collection->addAttributeToFilter("nb_sales", array("null" => true));
        return $collection;
    }
    
}