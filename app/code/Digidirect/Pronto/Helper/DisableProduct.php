<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;


class DisableProduct extends AbstractHelper
{


    protected $_productCollectionFactory;
    protected $productAction;
    protected $storeManager;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productAction,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productAction = $productAction;
        $this->storeManager = $storeManager;

    }

    public function toDisableProducts()
    {
//        $productCollection = $this->getProductCollection();
//        foreach ($productCollection as $product) {
//            //disable product
//        }

        try {
            $collection = $this->getProductCollection();
            $storeId = $this->storeManager->getStore()->getId();
            $ids = [];
            $i = 0;
            foreach ($collection as $item) {
                echo $item->getSku() . "<br/>";
                $ids[$i] = $item->getEntityId();
                echo $ids[$i]. "<br/>";
                $i++;
            }
            $this->productAction->updateAttributes($ids, array('status' => 2), $storeId);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function getProductCollection()
    {
        $now = new \DateTime();
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('date_update',['lt' => $now->format('Y-m-d')]);
        $collection->addAttributeToSelect('status', array('eq' => 2));
        $collection->setPageSize(10); // fetching only 3 products
        return $collection;
    }
}
