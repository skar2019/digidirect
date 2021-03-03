<?php

namespace Digidirect\PreOrder\Block\Checkout\Cart;

use Magento\Framework\View\Element\Template;

/**
 * Class Preorder
 *
 * @package Digidirect\PreOrder\Block\Checkout\Cart
 */
class Preorder extends Template
{
    const ITEM = 'item';

    /**
     * @var \Digidirect\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * Preorder constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Digidirect\PreOrder\Helper\Data $preOrderHelper,
        array $data = []
    ) {
        $this->preOrderHelper = $preOrderHelper;

        parent::__construct($context, $data);
    }

    /**
     * Check is can show block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return $this->preOrderHelper->getConfig()->preordersEnabled()
               && $this->preOrderHelper->isQuoteItemPreorder($this->getItem());
    }

    /**
     * Set item
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem|\Magento\Quote\Model\Quote\Item $quoteItem
     * @return $this
     */
    public function setItem(\Magento\Quote\Model\Quote\Item\AbstractItem $quoteItem)
    {
        $this->setData(static::ITEM, $quoteItem);
        return $this;
    }

    /**
     * Get item
     *
     * @return \Magento\Quote\Model\Quote\Item\AbstractItem|\Magento\Quote\Model\Quote\Item
     */
    public function getItem()
    {
        return $this->getData(static::ITEM);
    }

    /**
     * Get PreOrder note
     *
     * @return string
     */
    public function getPreorderNote()
    {
        return $this->preOrderHelper->getQuoteItemPreorderNote($this->getItem());
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->canShowBlock()) {
            return parent::_toHtml();
        }
        return '';
    }
}
