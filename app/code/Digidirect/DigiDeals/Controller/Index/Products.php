<?php

namespace Digidirect\DigiDeals\Controller\Index;

class Products extends \Magento\Framework\App\Action\Action
{
    protected $_productCollection;
    
    protected $logger;
    
    protected $categoryFactory;
    
    protected $categoryLinkRepository;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkRepository
    )
    {
        $this->logger = $logger;
        $this->_productCollection= $productCollection;
        $this->categoryFactory = $categoryFactory;
        $this->categoryLinkRepository = $categoryLinkRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $categoryId = [4080]; //digiDeals
        $category = $this->categoryFactory->create()->load($categoryId);
        $categoryProducts = $category->getProductCollection()->addAttributeToSelect('sku');

        foreach ($categoryProducts as $product) {
            $sku = $product->getSku();
            try {
                $isProductUnassigned = $this->categoryLinkRepository->deleteByIds($categoryId, $sku);
            } catch (Exception $ex) {
                $this->logger->info("digiDeals, " . $ex->getMessage());
                continue;
            }
        }
        
        $this->categoryLinkRepository->assignProductToCategories(148264, $categoryId);
        /*$productCollection = $this->getProductCollections();
        foreach ($productCollection as $product) {
            //print_r($product->getData('sku') . ',');
            $categoryLinkRepository->assignProductToCategories($product->getData('sku'), $categoryId);
        }*/
    }
    
    public function getProductCollections()
    {
        $collection = $this->_productCollection->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('marketplacer_seller', 20329);
        $collection->addMinimalPrice()->addFinalPrice();
        $collection->getSelect()->where("price_index.final_price < price_index.price")->orderRand();
        return $collection;
    }
    
}