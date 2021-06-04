<?php
namespace Ewave\GiftCardImage\Api;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Magento\Catalog\Model\Product;

/**
 * @api
 */
interface GiftCardImageRepositoryInterface
{
    /**
     * Create Gift Card Image
     *
     * @param GiftCardImageInterface $giftCardImage
     * @return GiftCardImageInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(GiftCardImageInterface $giftCardImage);

    /**
     * Get info about Gift Card Image by id
     *
     * @param int $giftCardImageId
     * @param bool $forceReload
     * @return GiftCardImageInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($giftCardImageId, $forceReload = false);

    /**
     * Delete Gift Card Image
     *
     * @param GiftCardImageInterface $giftCardImage
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(GiftCardImageInterface $giftCardImage);

    /**
     * @param int $giftCardImageId
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($giftCardImageId);

    /**
     * Get Gift Card Images list
     *
     * @return GiftCardImageInterface[]
     */
    public function getActiveGiftcardImages();

    /**
     * Get Gift Card Images list by product
     *
     * @param Product $product
     * @return GiftCardImageInterface[]
     */
    public function getGiftcardImagesByProduct(Product $product);
}
