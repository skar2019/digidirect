<?php

namespace Digidirect\DigiDeals\Block\Index;

use Magento\Framework\View\Element\Template;

class Search extends Template
{
    
    public function __construct(
        Template\Context $context, 
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Model\Product\Gallery\Processor $processor,
        array $data = [])
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->listProductBlock = $listProductBlock;
        $this->processor = $processor;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getProductCollectionSearchResult($searchTerm) 
    {
        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $productCollection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $productCollection->addAttributeToFilter('name', array('like' => '%'.$searchTerm.'%'));
        return $productCollection;
    }
    
    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }
    
    public function getProductPrice($product)
    {
        return $this->listProductBlock->getProductPrice($product);
    }
    
    public function getImage($product, $file)
    {
        return $this->processor->getImage($product, $file);
    }
    
}