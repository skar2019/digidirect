<?php

namespace Ewave\OrderImages\Block\Order\Items;

use Ewave\OrderImages\Helper\Data as Helper;
use Magento\Framework\App\ObjectManager;

/**
 * Class Image
 *
 * @package Ewave\OrderImages\Block\Order\Items
 */
class Image extends \Magento\Framework\View\Element\Template
{
    /**
     * Default image type
     */
    const DEFAULT_IMAGE_TYPE = 'cart_page_product_thumbnail';

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Ewave\OrderImages\Helper\Data
     */
    protected $helper;

    /**
     * Image constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     * @param Helper|null $helper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = [],
        Helper $helper = null
    ) {
        $this->_imageHelper = $imageHelper;

        parent::__construct($context, $data);
        $this->helper = $helper ?: ObjectManager::getInstance()->get(Helper::class);
    }

    /**
     * Get image type
     *
     * @return mixed|string
     */
    public function getImageType()
    {
        if ($imageType = $this->getData('image_type')) {
            return $imageType;
        }

        return self::DEFAULT_IMAGE_TYPE;
    }

    /**
     * Get item image
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string
     */
    public function getItemImage(\Magento\Sales\Model\Order\Item $item)
    {
        $product = $item->getProduct();
        if (1 == count($item->getChildrenItems())) {
            $child = current($item->getChildrenItems());
            if ($child->getProduct() &&
                ($image = $child->getProduct()->getImage()) &&
                $image !== 'no_selection'
            ) {
                $product = $child->getProduct();
            }
        }

        if (!$product) {
            $product = new \Magento\Framework\DataObject();
        }

        return $this->_imageHelper->init($product, $this->getImageType())->getUrl();
    }

    /**
     * @return bool
     */
    public function isModuleEnable()
    {
        return $this->helper->isModuleOrderImagesEnable();
    }
}
