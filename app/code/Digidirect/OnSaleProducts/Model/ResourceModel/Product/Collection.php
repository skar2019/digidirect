<?php
/**
 * GiaPhuGroup Co., Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GiaPhuGroup.com license that is
 * available through the world-wide-web at this URL:
 * https://www.giaphugroup.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Digidirect
 * @package     Digidirect_BestsellersProducts
 * @copyright   Copyright (c) 2018-2019 GiaPhuGroup Co., Ltd. All rights reserved. (http://www.giaphugroup.com/)
 * @license     https://www.giaphugroup.com/LICENSE.txt
 */

namespace Digidirect\OnSaleProducts\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Join sales_bestsellers_aggregated_yearly relation table to retrieve the bestseller products
     *
     * @param int $storeId
     * @return $this
     */
    public function getOnSaleProduct($storeId)
    {
        $this->getSelect()->joinLeft(
                ['catalogrule' => $this->getTable('catalogrule_product')],
                'e.entity_id = catalogrule.product_id'
        )->where('e.entity_id IS NOT NULL')->group('e.entity_id')->limit(25);
        return $this;
    }
}
