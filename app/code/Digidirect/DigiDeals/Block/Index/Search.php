<?php

namespace Digidirect\DigiDeals\Block\Index;

use Magento\Framework\View\Element\Template;
 
class Search extends Template
{
    
    protected $_productCollectionFactory;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ){
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getProductCollectionSearchResult() 
    {
        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $productCollection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $productCollection->addAttributeToFilter('name', array('like' => '%'.$searchTerm.'%'));
        return $productCollection;
    }
    
}