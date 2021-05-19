<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_PreOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\PreOrder\Model;

use Bss\PreOrder\Model\Attribute\Source\Order;

class PreOrderRepository implements \Bss\PreOrder\Api\PreOrderRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Bss\PreOrder\Helper\Data
     */
    protected $data;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * PreOrderRepository constructor.
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Bss\PreOrder\Helper\Data $data
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Bss\PreOrder\Helper\Data $data,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->productFactory = $productFactory;
        $this->data = $data;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @param string $sku
     * @param int|null $storeId
     * @return bool
     */
    public function checkIsPreOrderProduct($sku, $storeId = null)
    {
        if ($sku) {
            $product = $this->productFactory->create()->loadByAttribute('sku', $sku);
            $product->setStoreId($storeId);
            $isInStock = $product->getData('is_salable');
            $preOrder = $product->getData('preorder');
            $fromDate =  $product->getData('pre_oder_from_date');
            $toDate =  $product->getData('pre_oder_to_date');
            if ((
                $preOrder == Order::ORDER_YES
                    && $this->data->isAvailablePreOrderFromFlatData($fromDate, $toDate)
            )
                ||
                ($preOrder == Order::ORDER_OUT_OF_STOCK && $isInStock == 0)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int|null $storeId
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getListPreOrderProduct($storeId = null)
    {
        $collection  = $this->productCollectionFactory->create();
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToFilter('preorder', ['in' => ['1', '2']]);
        $productSkus = [];
        foreach ($collection as $product) {
            $isInStock = $this->data->getIsInStock($product->getId());
            $preorder = $product->getData('preorder');
            $isAvailablePreOrder = $this->data->isAvailablePreOrderFromFlatData(
                $product->getData('pre_oder_from_date'),
                $product->getData('pre_oder_to_date')
            );
            if ((!$isInStock && $preorder == Order::ORDER_OUT_OF_STOCK) ||
                ($preorder == Order::ORDER_YES && $isAvailablePreOrder)
            ) {
                $productSkus[] = $product->getSku();
            }
        }
        return $this->data->serializeClass()->serialize($productSkus);
    }
}
