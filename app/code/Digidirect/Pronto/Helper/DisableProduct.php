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
            //$storeId = $this->storeManager->getStore()->getId();
            $ids = [];
            $i = 0;
            foreach ($collection as $item) {
                //echo $item->getDateUpdate(). " - ". $item->getSku() . " - " .$item->getStatus() . "<br/>";
                $ids[$i] = $item->getEntityId();;
                $i++;
            }
            //$product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
            $this->productAction->updateAttributes($ids, array('status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED), 0);
            $this->productAction->updateAttributes($ids, array('status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED), 1);
            $this->productAction->updateAttributes($ids, array('status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED), 5);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function getProductCollection()
    {
        $now = new \DateTime();
        $collection = $this->_productCollectionFactory->create()
        ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->addAttributeToFilter('date_update',['lt' => $now->format('Y-m-d')]);
        //->setPageSize(12); // fetching only 3 products

        return $collection;
    }
}
