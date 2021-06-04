<?php

namespace Ewave\CriteoOneTag\Block;

/**
 * Class Basket
 *
 * @package Ewave\CriteoOneTag\Block
 */
class Basket extends AbstractTag
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Basket constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\Serialize\Serializer\Json     $serializer
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $registry, $serializer, $data);
    }

    /**
     * @return string
     */
    public function getItem()
    {
        $cartData = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $products = [];
        foreach ($cartData as $product) {
            $products[] = $this->getIdPriceQuantity(
                $product->getProduct()->getData('sku'),
                $product->getPriceInclTax(),
                $product->getQty()
            );
        }
        return $this->getStringItem($this->getDataType(), $this->serializer->serialize($products));
    }
}
