<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Company\Plugin\Catalog\Model\Product;

use Magento\Catalog\Model\Product\ReservedAttributeList;
use Magento\Company\Model\Company\ReservedAttributeList as CompanyReservedAttributeList;
use Magento\Eav\Model\Entity\Attribute;

/**
 * Plugin for reserved attribute list class
 *
 */
class ReservedAttributeListPlugin
{
    /**
     * @var CompanyReservedAttributeList
     */
    private $companyReservedAttributes;

    /**
     * @param CompanyReservedAttributeList $companyReservedAttributes
     */
    public function __construct(CompanyReservedAttributeList $companyReservedAttributes)
    {
        $this->companyReservedAttributes = $companyReservedAttributes;
    }

    /**
     * Check whether attribute code is reserved by company module
     *
     * @param ReservedAttributeList $subject
     * @param bool $result
     * @param Attribute $attribute
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsReservedAttribute(ReservedAttributeList $subject, bool $result, Attribute $attribute): bool
    {
        return $result ?: $this->companyReservedAttributes->isReservedAttribute($attribute);
    }
}
