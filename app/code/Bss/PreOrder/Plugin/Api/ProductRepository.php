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
 * @copyright  Copyright (c) 2018-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\PreOrder\Plugin\Api;

use Bss\PreOrder\Model\Attribute\Source\Order;

class ProductRepository
{
    /**
     * @var \Bss\PreOrder\Helper\Data
     */
    protected $helperApi;

    /**
     * @param \Bss\PreOrder\Helper\Data $helperApi
     */
    public function __construct(
        \Bss\PreOrder\Helper\Data $helperApi
    ) {
        $this->helperApi = $helperApi;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Catalog\Api\Data\ProductInterface $entity
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function afterGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $entity
    ) {
        $extensionAttributes = $entity->getExtensionAttributes();
        $extensionAttributes->setIsPreOrder($this->checkIsPreOrder($entity));
        $entity->setExtensionAttributes($extensionAttributes);
        return $entity;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Catalog\Api\Data\ProductSearchResultsInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function afterGetList(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductSearchResultsInterface $searchCriteria
    ) : \Magento\Catalog\Api\Data\ProductSearchResultsInterface {
        $products = [];
        foreach ($searchCriteria->getItems() as $entity) {
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setIsPreOrder($this->checkIsPreOrder($entity));
            $entity->setExtensionAttributes($extensionAttributes);
            $products[] = $entity;
        }
        $searchCriteria->setItems($products);
        return $searchCriteria;
    }

    /**
     * check product is preOrder
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $entity
     * @return bool
     */
    public function checkIsPreOrder($entity)
    {
        $isInStock = $entity->getData('is_salable');
        $preOrder = $entity->getData('preorder');
        if (($preOrder == Order::ORDER_YES && $this->helperApi->isAvailablePreOrderFromFlatData(
                    $entity['pre_oder_from_date'],
                    $entity['pre_oder_to_date']
                )) || ($preOrder == Order::ORDER_OUT_OF_STOCK && !$isInStock)) {
            return true;
        }
        return false;
    }
}
