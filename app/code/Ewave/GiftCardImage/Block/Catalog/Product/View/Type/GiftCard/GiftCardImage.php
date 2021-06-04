<?php
namespace Ewave\GiftCardImage\Block\Catalog\Product\View\Type\GiftCard;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Ewave\GiftCardImage\Model\QuoteItem;
use Ewave\GiftCardImage\Helper\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class GiftCardImage extends AbstractView
{
    /**
     * @var GiftCardImageRepositoryInterface
     */
    protected $giftCardImageRepository;

    /**
     * @var QuoteItem
     */
    protected $quoteItem;

    /**
     * @var ImageFactory
     */
    protected $imageHelperFactory;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var []
     */
    protected $_giftCardImages;

    /**
     * @var string|int
     */
    protected $_preConfiguredValue;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param GiftCardImageRepositoryInterface $giftCardImageRepository
     * @param QuoteItem\Proxy $quoteItem
     * @param ImageFactory $imageHelperFactory
     * @param JsonHelper $jsonHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        GiftCardImageRepositoryInterface $giftCardImageRepository,
        QuoteItem\Proxy $quoteItem,
        ImageFactory $imageHelperFactory,
        JsonHelper $jsonHelper,
        array $data = []
    ) {
        $this->giftCardImageRepository = $giftCardImageRepository;
        $this->quoteItem = $quoteItem;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    /**
     * @return mixed
     */
    public function getGiftcardImages()
    {
        if ($this->_giftCardImages === null) {
            $this->_giftCardImages = $this->giftCardImageRepository->getGiftcardImagesByProduct($this->getProduct());
        }
        return $this->_giftCardImages;
    }

    /**
     * @return string|int
     */
    public function getPreConfiguredValue()
    {
        if ($this->_preConfiguredValue === null) {
            if ($this->getProduct()->getConfigureMode()) {
                $this->_preConfiguredValue = $this->quoteItem
                    ->getGiftcardImageByQuoteItemId((int)$this->_request->getParam('id'))
                    ->getId();
            } else {
                $this->_preConfiguredValue = $this->getProduct()->getPreconfiguredValues()
                    ->getData(GiftCardImageInterface::PUBLIC_KEY);
            }
        }
        return $this->_preConfiguredValue;
    }

    /**
     * @return \Ewave\GiftCardImage\Helper\Image
     */
    public function getImageHelper()
    {
        return $this->imageHelperFactory->create();
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $result = [];
        $images = $this->getGiftcardImages();
        foreach ($images as $image) {
            /** @var \Ewave\GiftCardImage\Model\GiftCardImage $image */
            $result[] = [
                'thumb' => $this->getImageHelper()
                    ->init($image, 'image', ['type' => 'ewave_giftcard_image_thumbnail'])->getUrl(),
                'img' => $this->getImageHelper()
                    ->init($image, 'image', ['type' => 'ewave_giftcard_image_gallery'])->getUrl(),
                'full' => $this->getImageHelper()
                    ->init($image, 'image', ['type' => 'ewave_giftcard_image_full'])->getUrl(),
                'caption' => $image->getTitle(),
                'isMain' => false,
                'imgId' => $image->getId()
            ];
        }
        return $this->jsonHelper->jsonEncode($result);
    }
}
