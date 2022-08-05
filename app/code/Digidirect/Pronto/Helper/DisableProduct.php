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

    public function toDisableProducts($test)
    {

        echo "To Disable <br>";
        try {
            $collection = $this->getProductCollection();
            //$storeId = $this->storeManager->getStore()->getId();
            $ids = [];
            $i = 0;
            foreach ($collection as $item) {
                if($test)
                {
                    echo $item->getDateUpdate(). " - ". $item->getSku() . " - " .$item->getStatus() . "<br/>";
                }
                $ids[$i] = $item->getEntityId();
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

    public function toEnableProducts($test)
    {

        try {
            $collection = $this->getProductCollectionToEnable();
            //$storeId = $this->storeManager->getStore()->getId();
            $ids = [];
            $i = 0;
            foreach ($collection as $item) {
                if($test)
                {
                    echo $item->getDateUpdate(). " - ". $item->getSku() . " - " .$item->getStatus() . "<br/>";
                }
                
                $ids[$i] = $item->getEntityId();
                $i++;
            }
            //$product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
            $this->productAction->updateAttributes($ids, array('status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED), 0);
            $this->productAction->updateAttributes($ids, array('status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED), 1);
            $this->productAction->updateAttributes($ids, array('status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED), 5);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }
    
    public function getProductCollection()
    {
        //if there's still issue on some products not being disabled, adjust the date close to most recent date. e,g. date yesterday or -2 day
        $date = date("Y-m-d", strtotime("-3 day"));
        $collection = $this->_productCollectionFactory->create()
        ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->addAttributeToFilter('date_update',array('lteq' => $date));
        //->setPageSize(110); // fetching only 3 products

        return $collection;
    }
    
    public function getProductCollectionToEnable()
    {

        $date = date("Y-m-d", strtotime("-2 day"));
        $collection = $this->_productCollectionFactory->create()
        ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)
        ->addAttributeToFilter('date_update',array('gteq' => $date));
        //->setPageSize(12); // fetching only 3 products

        return $collection;
    }
    
}
