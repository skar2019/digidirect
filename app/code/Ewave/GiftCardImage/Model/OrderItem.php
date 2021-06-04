<?php
namespace Ewave\GiftCardImage\Model;

use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use Magento\Framework\Model\AbstractModel;

class OrderItem extends AbstractModel
{
    const GIFTCARD_IMAGE_ID = 'giftcard_image_id';

    const ORDER_ITEM_ID = 'order_item_id';

    /**
     * @var GiftCardImageRepositoryInterface
     */
    protected $giftCardImageRepository;

    /**
     * QuoteItem constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param GiftCardImageRepositoryInterface $giftCardImageRepository
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        GiftCardImageRepositoryInterface $giftCardImageRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->giftCardImageRepository = $giftCardImageRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\GiftCardImage\Model\ResourceModel\OrderItem');
    }

    /**
     * @param int $orderItemId
     * @return bool|\Ewave\GiftCardImage\Api\Data\GiftCardImageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGiftcardImageByOrderItemId($orderItemId)
    {
        $giftcardId = $this->_getResource()->getGiftcardImageIdByOrderItemId($orderItemId);
        try {
            return $this->giftCardImageRepository->getById($giftcardId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param Order $order
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveDataFromQuoteToOrder(Order $order)
    {
        $quoteItemIds = [];
        foreach ($order->getItems() as $item) {
            $quoteItemIds[] = $item->getQuoteItemId();
        }

        if (!empty($quoteItemIds)) {
            $this->_getResource()->moveDataFromQuoteToOrder($quoteItemIds);
        }
    }
}
