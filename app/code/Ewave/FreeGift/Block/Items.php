<?php

namespace Ewave\FreeGift\Block;

class Items extends \Magento\Framework\View\Element\Template
{
    const TEMPLATE_PATH = 'cart/popup/%s.phtml';

    /**
     * @var \Ewave\FreeGift\Model\Cart
     */
    protected $_giftCart;

    /**
     * @var \Ewave\FreeGift\Helper\Config
     */
    protected $_helperConfig;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_helperImage;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $_urlHelper;

    /**
     * Items constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\FreeGift\Model\Cart $giftCart
     * @param \Ewave\FreeGift\Helper\Config $helperConfig
     * @param \Magento\Catalog\Helper\Image $helperImage
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ewave\FreeGift\Model\Cart $giftCart,
        \Ewave\FreeGift\Helper\Config $helperConfig,
        \Magento\Catalog\Helper\Image $helperImage,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_giftCart = $giftCart;
        $this->_helperConfig = $helperConfig;
        $this->_helperImage = $helperImage;
        $this->_urlHelper = $urlHelper;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $items = $this->getItems();
        if (count($items)) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return $this|bool|\Magento\Framework\Data\Collection\AbstractDb|null
     */
    public function getItems()
    {
        return $this->_giftCart->getNewFreeGiftItems();
    }

    /**
     * @return $this|bool|\Magento\Framework\Data\Collection\AbstractDb|null
     */
    public function getRules()
    {
        return $this->_giftCart->getNewFreeGiftRules();
    }

    /**
     * @return \Magento\Catalog\Helper\Image
     */
    public function getImageHelper()
    {
        return $this->_helperImage;
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('ewave_gift/cart/add');
    }

    /**
     * @return string
     */
    public function getCurrentBase64Url()
    {
        return $this->_urlHelper->getCurrentBase64Url();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        if ($template = $this->_helperConfig->getPopupTemplate()) {
            return sprintf(self::TEMPLATE_PATH, $template);
        }
        return parent::getTemplate();
    }
}
