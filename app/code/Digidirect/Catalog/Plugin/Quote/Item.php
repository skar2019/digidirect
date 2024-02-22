<?php

namespace Digidirect\Catalog\Plugin\Quote;

use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Catalog\Model\Product;
use Digidirect\FreeGift\Model\Cart\Item as CartItem;

class Item
{
    /**
     * @var CartItem
     */
    protected $_giftItem;
    
    
    protected $logger;

    /**
     * Item constructor.
     *
     * @param CartItem $giftItem
     */
    public function __construct
    (   
        CartItem $giftItem,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_giftItem = $giftItem;
        $this->logger = $logger;
    }

    /**
     * @param QuoteItem $subject
     * @param mixed $value
     * @return array
     */
    public function beforeSetPrice(QuoteItem $subject, $value)
    {
        $this->logger->info('beforeSetPrice!');
        $this->logger->info('$value: ' . $value);
        $this->logger->info('isFreeGiftItem: ' . $this->_giftItem->isFreeGiftItem($subject));
        if ($this->_giftItem->isFreeGiftItem($subject)) {
            $this->logger->info('This is free gift!');
            return [5];
        }
        return [$value];
    }
}
