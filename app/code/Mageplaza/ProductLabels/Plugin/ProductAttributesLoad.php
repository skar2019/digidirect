<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Plugin;

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Mageplaza\ProductLabels\Helper\Data;

/**
 * Class ProductAttributesLoad
 * @package Mageplaza\ProductLabels\Plugin
 */
class ProductAttributesLoad
{
    /**
     * @var ProductExtensionFactory
     */
    private $extensionFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * ProductAttributesLoad constructor.
     *
     * @param Data $helperData
     * @param ProductExtensionFactory $extensionFactory
     */
    public function __construct(
        Data $helperData,
        ProductExtensionFactory $extensionFactory
    ) {
        $this->helperData       = $helperData;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Loads product entity extension attributes
     *
     * @param ProductInterface $entity
     * @param ProductExtensionInterface|null $extension
     *
     * @return ProductExtensionInterface
     * @SuppressWarnings(Unused)
     */
    public function afterGetExtensionAttributes(
        ProductInterface $entity,
        ProductExtensionInterface $extension = null
    ) {
        if (!$this->helperData->isEnabled()) {
            return $extension;
        }

        if ($extension === null) {
            $extension = $this->extensionFactory->create();
        }

        return $extension;
    }
}
