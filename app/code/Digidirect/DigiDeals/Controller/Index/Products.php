<?php

namespace Digidirect\DigiDeals\Controller\Index;

class Products extends \Magento\Framework\App\Action\Action
{
    protected $_productCollection;
    
    protected $logger;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
    )
    {
        $this->logger = $logger;
        $this->_productCollection= $productCollection;
        parent::__construct($context);
    }

    public function execute()
    {
        $productCollection = $this->getProductCollections();
        foreach ($productCollection as $product) {
            print_r($product->getData('sku') . ',');
        }
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