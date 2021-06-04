<?php
namespace Ewave\GiftCardImage\Block\Catalog\Product;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Model\QuoteItem as GiftcardQuoteItem;
use Ewave\GiftCardImage\Helper\ImageFactory as GiftcardHelperFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * @var CartItemInterface
     */
    protected $quoteItem;

    /**
     * @var GiftcardQuoteItem
     */
    protected $giftcardQuoteItem;

    /**
     * @var GiftcardHelperFactory
     */
    protected $giftcardHelperFactory;

    /**
     * @var string
     */
    protected $giftcardImageType;

    /**
     * @param HelperFactory $helperFactory
     * @param ImageFactory $imageFactory
     * @param GiftcardHelperFactory $giftcardHelperFactory
     * @param GiftcardQuoteItem $giftcardQuoteItem
     * @param string $giftcardImageType
     */
    public function __construct(
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        GiftcardHelperFactory $giftcardHelperFactory,
        GiftcardQuoteItem $giftcardQuoteItem,
        $giftcardImageType = null
    ) {
        parent::__construct($helperFactory, $imageFactory);
        $this->giftcardHelperFactory = $giftcardHelperFactory;
        $this->giftcardQuoteItem = $giftcardQuoteItem;
        $this->giftcardImageType = $giftcardImageType;
    }

    /**
     * @param CartItemInterface|AbstractItem $quoteItem
     * @return $this
     */
    public function setQuoteItem(CartItemInterface $quoteItem)
    {
        $this->quoteItem = $quoteItem;
        return $this;
    }

    /**
     * @param GiftCardImageInterface $giftCardImage
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function createGiftCardImage(GiftCardImageInterface $giftCardImage)
    {
        $attributes = [];
        if ($this->giftcardImageType) {
            $attributes['type'] = $this->giftcardImageType;
        }

        /** @var \Ewave\GiftCardImage\Helper\Image $helper */
        $helper = $this->giftcardHelperFactory->create()
            ->init($giftCardImage, GiftCardImageInterface::IMAGE, $attributes);

        $template = $helper->getFrame()
            ? 'Magento_Catalog::product/image.phtml'
            : 'Magento_Catalog::product/image_with_borders.phtml';

        $imagesize = $helper->getResizedImageInfo();

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
            ],
        ];

        return ObjectManager::getInstance()->create(\Magento\Catalog\Block\Product\Image::class, $data);
    }

    /**
     * Create image block
     *
     * @param Product|null $product
     * @param string|null $imageId
     * @param array|null $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create(Product $product = null, string $imageId = null, array $attributes = null)
    {
        if (null !== $this->quoteItem) {
            $giftCardImage = $this->giftcardQuoteItem->getGiftcardImageByQuoteItemId($this->quoteItem->getItemId());
            $this->quoteItem = null;
            if ($giftCardImage) {
                return $this->createGiftCardImage($giftCardImage);
            }
        }

        return parent::create($product, $imageId, $attributes);
    }
}
