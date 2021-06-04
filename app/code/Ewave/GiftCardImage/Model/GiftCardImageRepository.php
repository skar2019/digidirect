<?php
namespace Ewave\GiftCardImage\Model;

use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Model\Media\ImageProcessorFactory;
use Ewave\GiftCardImage\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftCardImageRepository implements GiftCardImageRepositoryInterface
{
    /**
     * @var GiftCardImageInterface[]
     */
    protected $instances = [];

    /**
     * @var \Ewave\GiftCardImage\Model\GiftCardImageFactory
     */
    protected $giftCardImageFactory;

    /**
     * @var \Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage
     */
    protected $giftCardImageResource;

    /**
     * @var \Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage\CollectionFactory
     */
    protected $giftCardImageCollectionFactory;

    /**
     * @var ImageProcessorFactory
     */
    protected $imageProcessorFactory;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @param \Ewave\GiftCardImage\Model\GiftCardImageFactory $giftCardImageFactory
     * @param \Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage $giftCardImageResource
     * @param \Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage\CollectionFactory $giftCardImageCollectionFactory
     * @param ImageProcessorFactory $imageProcessorFactory
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        \Ewave\GiftCardImage\Model\GiftCardImageFactory $giftCardImageFactory,
        \Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage $giftCardImageResource,
        \Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage\CollectionFactory $giftCardImageCollectionFactory,
        ImageProcessorFactory $imageProcessorFactory,
        ImageHelper $imageHelper
    ) {
        $this->giftCardImageFactory = $giftCardImageFactory;
        $this->giftCardImageResource = $giftCardImageResource;
        $this->giftCardImageCollectionFactory = $giftCardImageCollectionFactory;
        $this->imageProcessorFactory = $imageProcessorFactory;
        $this->imageHelper = $imageHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(GiftCardImageInterface $giftCardImage)
    {
        try {
            $this->_processImage($giftCardImage);
            $this->giftCardImageResource->save($giftCardImage);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save Gift Card Image: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$giftCardImage->getId()]);
        return $this->getById($giftCardImage->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getById($giftCardImageId, $forceReload = false)
    {
        if (!isset($this->instances[$giftCardImageId]) || $forceReload) {
            /** @var GiftCardImageInterface $giftCardImage */
            $giftCardImage = $this->giftCardImageFactory->create();
            $giftCardImage->load($giftCardImageId);
            if (!$giftCardImage->getId()) {
                throw NoSuchEntityException::singleField('id', $giftCardImageId);
            }
            $this->instances[$giftCardImageId] = $giftCardImage;
        }
        return $this->instances[$giftCardImageId];
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->giftCardImageCollectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveGiftcardImages()
    {
        return $this->getCollection()
            ->addFieldToFilter(GiftCardImageInterface::STATUS, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftcardImagesByProduct(Product $product)
    {
        return $this->getActiveGiftcardImages()
            ->addFieldToFilter(GiftCardImageInterface::ID, ['in' => $product->getGiftcardImages()]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(GiftCardImageInterface $giftCardImage)
    {
        try {
            $id = $giftCardImage->getId();
            $this->giftCardImageResource->delete($giftCardImage);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete Gift Card Image with id %1',
                    $giftCardImage->getId()
                ),
                $e
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($giftCardImageId)
    {
        try {
            $giftCardImage = $this->getById($giftCardImageId);
            $this->giftCardImageResource->delete($giftCardImage);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete Gift Card Image with id %1',
                    $giftCardImageId
                ),
                $e
            );
        }
        unset($this->instances[$giftCardImageId]);
        return true;
    }

    /**
     * Process image
     * @param GiftCardImageInterface $giftCardImage
     * @return string[]|null
     * @throws LocalizedException
     */
    protected function _processImage(GiftCardImageInterface $giftCardImage)
    {
        $image = null;
        foreach (ImageHelper::IMAGES_INFO as $key => $infoKey) {
            if ($imageFile = $this->imageHelper->getImageInfo($giftCardImage, $infoKey)) {
                $result = $this->imageProcessorFactory->create()->save($imageFile);
                //Result can be empty array and it mean that image stay the same
                if (isset($result['error'])) {
                    throw new LocalizedException($result['error']);
                } elseif (!empty($result['file'])) {
                    // Remember old image and delete earlier
                    if ($giftCardImage->hasData($key)
                        && ($img = $giftCardImage->getData($key)) && $img != $result['file']
                    ) {
                        $image[] = $img;
                    }
                    $giftCardImage->setData($key, $result['file']);
                }
            } else {
                // It mean that image was deleted or it wasn't uploaded
                if ($img = $giftCardImage->getData($key)) {
                    $image[] = $img;
                }

                $giftCardImage->setData($key, null);
            }
        }
        return $image;
    }
}
