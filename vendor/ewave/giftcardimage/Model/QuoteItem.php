<?php
namespace Ewave\GiftCardImage\Model;

use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Ewave\GiftCardImage\Model\ResourceModel\OrderItem;
use Magento\Framework\Model\AbstractModel;

class QuoteItem extends AbstractModel
{
    const GIFTCARD_IMAGE_ID = 'giftcard_image_id';

    const QUOTE_ITEM_ID = 'quote_item_id';

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
        $this->_init('Ewave\GiftCardImage\Model\ResourceModel\QuoteItem');
    }

    /**
     * @param int $quoteItemId
     * @return bool|\Ewave\GiftCardImage\Api\Data\GiftCardImageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGiftcardImageByQuoteItemId($quoteItemId)
    {
        $giftcardId = $this->_getResource()->getGiftcardImageIdByQuoteItemId($quoteItemId);
        try {
            return $this->giftCardImageRepository->getById($giftcardId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param int $quoteItemId
     * @param int $giftCardImageId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveGiftCardImage($quoteItemId, $giftCardImageId)
    {
        //todo: add checks
        return $this->_getResource()->saveGiftCardImage($quoteItemId, $giftCardImageId);
    }
}
