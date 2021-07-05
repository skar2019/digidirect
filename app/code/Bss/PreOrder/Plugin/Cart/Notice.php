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
namespace Bss\PreOrder\Plugin\Cart;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Bss\PreOrder\Model\Attribute\Source\Order;
use Magento\Framework\Exception\NoSuchEntityException;

class Notice
{
    /**
     * @var \Bss\PreOrder\Helper\Data
     */
    protected $helper;

    /**
     * @var \Bss\PreOrder\Model\Factory
     */
    protected $factory;

    /**
     * Notice constructor.
     * @param \Bss\PreOrder\Helper\Data $helper
     * @param \Bss\PreOrder\Model\Factory $factory
     */
    public function __construct(
        \Bss\PreOrder\Helper\Data $helper,
        \Bss\PreOrder\Model\Factory $factory
    ) {
        $this->helper = $helper;
        $this->factory = $factory;
    }

    /**
     * @param $subject
     * @param $item
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetItemHtml($subject, $item)
    {
        if ($this->helper->isEnable()) {
            try {
                if ($item->getProduct()->getTypeId() == Configurable::TYPE_CODE) {
                    /* Load configurable product by sku */
                    $productSku = $item->getSku();
                    $product = $this->helper->getProductBySku($productSku);
                } else {
                    /* Load other product by id, becase some product has custom option sku */
                    $productId = $item->getProduct()->getId();
                    $product = $this->helper->getProductById($productId);
                }
                $isInStock = $this->helper->getIsInStock($product->getId());
                $preOrder = $product->getData('preorder');

                $availabilityPreOrder = $this->helper->isAvailablePreOrder($product->getId());
                $show_mess = $this->helper->isPreOrder($preOrder, $isInStock, $availabilityPreOrder);

                if ($preOrder == Order::ORDER_OUT_OF_STOCK && !$isInStock) {
                    $stock = $this->helper->getStockItem($product->getId())->getQty();
                    if (!$this->helper->checkVersion()) {
                        $stockData =  $this->factory->create()->execute($item->getSku());
                        $stock = $stockData[0]["qty"];
                    }
                    if ($item->getQty() > $stock) {
                        $show_mess = true;
                    }
                }
                if ($show_mess) {
                    $item->setMessage($this->helper->getNote());
                }
            } catch (NoSuchEntityException $e) {
                return [$item];
            }
        }
        return [$item];
    }
}
