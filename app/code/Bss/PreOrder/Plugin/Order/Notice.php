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
namespace Bss\PreOrder\Plugin\Order;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class Notice
{
    /**
     * @var \Bss\PreOrder\Helper\Data
     */
    protected $helper;

    /**
     * OrderNotice constructor.
     * @param \Bss\PreOrder\Helper\Data $helper
     */
    public function __construct(
        \Bss\PreOrder\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Extra Note Pre Order Product
     *
     * @param \Magento\Sales\Block\Items\AbstractItems $subject
     * @param \Magento\Framework\DataObject $item
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetItemHtml($subject, $item)
    {
        if ($this->helper->isEnable()) {
            if ($item->getProductType() == Configurable::TYPE_CODE) {
                $product = $this->helper->getProductBySku($item->getProductOptionByCode('simple_sku'));
            } else {
                $product = $this->helper->getProductById($item->getProductId());
            }

            if ($product && $product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
                $isInStock = $this->helper->getIsInStock($product->getId());
                $preOrder = $product->getData('preorder');

                $message = $this->helper->replaceVariableX(
                    $this->helper->getNote(),
                    $this->helper->formatDate($product->getData('pre_oder_from_date')),
                    $this->helper->formatDate($product->getData('pre_oder_to_date'))
                );
                $availabilityPreOrder = $this->helper->isAvailablePreOrder($product->getId());
                if ($this->helper->isPreOrder($preOrder, $isInStock, $availabilityPreOrder)) {
                    return [$item->setDescription($message)];
                }
            }
        }
        return [$item];
    }
}
