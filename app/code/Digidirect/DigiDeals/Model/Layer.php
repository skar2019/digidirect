<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MyCustomCollection
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Digidirect\DigiDeals\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Layer extends \Magento\Catalog\Model\Layer
{
    protected $logger;
    
    public function __construct(
        \Magento\Catalog\Model\Layer\ContextInterface $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $productCollectionFactory,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->logger = $logger;
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
    }
    
    public function getStateKey()
    {
        if (!$this->_stateKey) {
            $this->_stateKey = $this->stateKeyGenerator->toString($this->getCurrentCategory());
        }
        $this->logger->info("this->_stateKey: " . $this->_stateKey); 
        return $this->_stateKey;
    }
    
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->collectionProvider->getCollection($this->getCurrentCategory());
            //$collection->addAttributeToSelect('*')->addFinalPrice();
            //$collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            //$collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            //$collection->getSelect()->where("price_index.final_price < price")->limit(3);
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection->limit(3);
        }
        
        return $collection;
    }
    
}
