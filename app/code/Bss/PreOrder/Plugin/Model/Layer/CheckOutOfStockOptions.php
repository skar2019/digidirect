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
namespace Bss\PreOrder\Plugin\Model\Layer;

use Magento\Catalog\Model\Product;
use Bss\PreOrder\Helper\Data as PreOrderHelper;

class CheckOutOfStockOptions
{
    /**
     * @var PreOrderHelper
     */
    protected $preOrderHelper;

    /**
     * CheckStockOptions constructor.
     * @param PreOrderHelper $preOrderHelper
     */
    public function __construct(
        PreOrderHelper $preOrderHelper
    ) {
        $this->preOrderHelper = $preOrderHelper;
    }

    /**
     * Check Out of Stock Options
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @param mixed $collection
     * @return \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetProductCollection(
        \Magento\Catalog\Model\Layer $layer,
        $collection
    ) {
        if ($this->preOrderHelper->isEnable()) {
            /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection */
            $productCollection = clone $collection;
            $ignoreProductIds = [];
            /** @var Product $product */
            foreach ($productCollection as $product) {
                $productId = $this->dontShowProductId($product);
                if ($productId) {
                    $ignoreProductIds[] = $productId;
                }
            }
            if (!empty($ignoreProductIds)) {
                $collection->addAttributeToFilter('entity_id', ['nin' => $ignoreProductIds]);
            }
        }
        return $collection;
    }

    /**
     * Check Ignore Product Id
     *
     * @param Product $product
     * @return bool|int
     */
    protected function dontShowProductId($product)
    {
        if (!$product->getData('preorder') &&
            !$product->isSaleable() &&
            !$product->isAvailable() &&
            $this->preOrderHelper->isDisplayOutOfStockProduct()) {
            return (int)$product->getId();
        }
        return false;
    }
}
