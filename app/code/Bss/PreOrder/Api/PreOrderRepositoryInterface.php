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

namespace Bss\PreOrder\Api;

interface PreOrderRepositoryInterface
{
    /**
     * Check Product Is PreOrder
     * @param string $sku
     * @param int|null $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkIsPreOrderProduct($sku, $storeId = null);

    /**
     * Get list PreOrder Product
     * @param int|null $storeId
     * @return mixed
     */
    public function getListPreOrderProduct($storeId = null);
}
