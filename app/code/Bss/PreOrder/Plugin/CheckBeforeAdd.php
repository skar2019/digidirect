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
namespace Bss\PreOrder\Plugin;

use Bss\PreOrder\Helper\Data;
use Magento\Checkout\Model\Cart;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CheckBeforeAdd
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var boolean
     */
    protected $preOrderItem;

    /**
     * @var boolean
     */
    protected $preOrderCartItem;

    /**
     * CheckBeforeAdd constructor.
     * @param Data $helper
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        Data $helper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->configurable = $configurable;
    }

    /**
     * Validate Product Before Add
     *
     * @param Cart $subject
     * @param mixed $productInfo
     * @param array $requestInfo
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        if ($this->helper->isEnable()) {
            if (!$this->helper->isMix()) {
                $cartItems = $subject->getQuote()->getAllItems();
                if ($this->request->getParam('super_group')) {
                    $this->validateForGroupProduct($cartItems, $requestInfo);
                } else {
                    if (!empty($cartItems)) {
                        $preOrderItem = $this->checkPreOrderItem($productInfo, $requestInfo);
                        $this->validateWithCart($cartItems, $preOrderItem);
                    }
                }
            }
        }
        return [$productInfo, $requestInfo];
    }

    /**
     * Validate For Group Product
     *
     * @param array $cartItems
     * @param array $requestInfo
     * @throws LocalizedException
     */
    public function validateForGroupProduct($cartItems, $requestInfo)
    {
        $countNormalProduct = $countPreOrder = 0;
        foreach ($requestInfo['super_group'] as $key => $value) {
            if ($value) {
                if (!empty($cartItems)) {
                    /* Validate with product in Cart */
                    $preOrderItem = $this->checkPreOrderGroup($key, $value);
                    $this->validateWithCart($cartItems, $preOrderItem);
                } else {
                    /* Validate with other child product in request if Cart no items */
                    $isPreOrderItem = $this->checkPreOrderGroup($key, $value)['isPreOrderItem'];
                    if ($isPreOrderItem) {
                        $countPreOrder++;
                    } else {
                        $countNormalProduct++;
                    }
                }
            }
        }
        if (empty($cartItems)) {
            if ($countNormalProduct && $countPreOrder) {
                $this->returnErrorMess();
            }
        }
    }

    /**
     * Validate Request Product With Items In Cart
     *
     * @param array $cartItems
     * @param array $preOrderItem
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function validateWithCart($cartItems, $preOrderItem)
    {
        $typeConfi = Configurable::TYPE_CODE;
        $isPreOrderItem = $preOrderItem['isPreOrderItem'];
        foreach ($cartItems as $item) {
            $productId = $item->getProduct()->getId();
            $product = $item;
            if ($item->getProduct()->getTypeId() == $typeConfi) {
                $requestInfo =$item->getBuyRequest();
                $product = $this->helper->getProductById($productId);
                $product = $this->configurable->getProductByAttributes(
                    $requestInfo['super_attribute'],
                    $product
                );
                $productId = $product->getId();
                $preOrderProduct = $this->helper->getPreOrder($productId);
                $qtyProduct = $this->helper->getProductSalableQty($product, $productId);
                if (!$isPreOrderItem && $preOrderItem['productId'] == $productId
                    && ($preOrderItem['qtyOrder'] +$requestInfo['qty'] > $qtyProduct)) {
                    $this->preOrderItem = true;
                    continue;
                }
                if ($preOrderProduct == 2 && $requestInfo['qty'] > $qtyProduct) {
                    $this->isPreOrderCart($isPreOrderItem, 1);
                    $this->preOrderCartItem = true;
                    continue;
                }
            }
            $isPreOrderCart = $this->checkPreOrderCartItem($product, $productId, $preOrderItem);
            if ($productId == $preOrderItem['productId']) {
                continue;
            }
            $this->isPreOrderCart($isPreOrderItem, $isPreOrderCart);
        }
        $this->checkDisplayMessage();
    }

    /**
     * @throws LocalizedException
     */
    private function checkDisplayMessage()
    {
        if ($this->preOrderItem && $this->preOrderCartItem) {
            $this->preOrderItem = false;
            $this->preOrderCartItem = false;
            $this->returnErrorMess();
        }
    }

    /**
     * Validate
     *
     * @param bool $isPreOrderItem
     * @param bool $isPreOrderCart
     * @throws LocalizedException
     */
    protected function isPreOrderCart($isPreOrderItem, $isPreOrderCart)
    {
        if (($isPreOrderItem && !$isPreOrderCart) || (!$isPreOrderItem && $isPreOrderCart)) {
            $this->returnErrorMess();
        }
    }

    /**
     * Return Error Message
     *
     * @throws LocalizedException
     */
    protected function returnErrorMess()
    {
        $message = "We could not add both pre-order and regular items to an order.";
        throw new LocalizedException(__($message));
    }

    /**
     * @param mixed $product
     * @param array $requestInfo
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function checkPreOrderItem($product, $requestInfo)
    {
        $isPreOrderItem = $this->request->getParam('is_preorder');
        $qtyOrder = isset($requestInfo['qty']) ? $requestInfo['qty'] : 1;
        $productId = $requestInfo['product'];
        if (!$isPreOrderItem && is_array($requestInfo)) {
            if ($product->getTypeId() == Configurable::TYPE_CODE) {
                $product = $this->helper->getProductById($productId);
                $product = $this->configurable->getProductByAttributes(
                    $requestInfo['super_attribute'],
                    $product
                );
                $productId = $product->getId();
            }
            $preOrderProduct = $this->helper->getPreOrder($productId);
            $qtyProduct = $this->helper->getProductSalableQty($product, $productId);
            if ($preOrderProduct == 2 && $qtyOrder > $qtyProduct) {
                $isPreOrderItem = 1;
            }
        }
        return [
            'isPreOrderItem' => (bool)$isPreOrderItem,
            'qtyOrder' => $qtyOrder,
            'productId' => $productId
        ];
    }

    /**
     * @param mixed $item
     * @param int $productId
     * @param array $preOrderItem
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function checkPreOrderCartItem($item, $productId, $preOrderItem)
    {
        $preOrderCart = $this->helper->getPreOrder($productId);
        $inStockCart = $this->helper->getIsInStock($productId);
        $availabilityPreOrder = $this->helper->isAvailablePreOrder($productId);
        $isPreOrderCart = $this->helper->isPreOrder($preOrderCart, $inStockCart, $availabilityPreOrder);
        if ($inStockCart && $preOrderCart == 2) {
            $qtyProduct = $this->helper->getProductSalableQty($item, $productId);
            if ($item->getQty() > $qtyProduct) {
                $isPreOrderCart = true;
            }
            $isPreOrderItem = $preOrderItem['isPreOrderItem'];
            if (!$isPreOrderItem && $preOrderItem['productId'] == $productId
                && ($preOrderItem['qtyOrder'] + $item->getQty() > $qtyProduct)) {
                $this->preOrderItem = true;
            }
        }
        if (!$isPreOrderCart) {
            $this->preOrderCartItem = true;
        }
        return $isPreOrderCart;
    }

    /**
     * @param int $key
     * @param float $value
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function checkPreOrderGroup($key, $value)
    {
        $isPreOrderItem = $this->request->getParam('is_preorder_group_' . $key);
        $productId = $key;
        if (!$isPreOrderItem) {
            $product = $this->helper->getProductById($key);
            $preOrderProduct = $this->helper->getPreOrder($productId);
            $qtyProduct = $this->helper->getProductSalableQty($product, $productId);
            if ($preOrderProduct == 2 && $value > $qtyProduct) {
                $isPreOrderItem = 1;
            }
        }
        return [
            'isPreOrderItem' => (bool)$isPreOrderItem,
            'qtyOrder' => $value,
            'productId' => $productId
        ];
    }
}
