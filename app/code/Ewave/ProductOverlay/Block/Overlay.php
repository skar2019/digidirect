<?php

namespace Ewave\ProductOverlay\Block;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Overlay
 * @package Ewave\ProductOverlay\Block
 */
class Overlay extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ewave\ProductOverlay\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ewave\ProductOverlay\Model\Overlays
     */
    protected $_overlay;

    /**
     * Overlay constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\ProductOverlay\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ewave\ProductOverlay\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper      = $helper;
        $this->setTemplate('Ewave_ProductOverlay::overlay.phtml');
    }

    /**
     * @param \Ewave\ProductOverlay\Model\Overlays $overlay
     * @return $this
     */
    public function setOverlay(\Ewave\ProductOverlay\Model\Overlays $overlay)
    {
        $this->_overlay = $overlay;
        return $this;
    }

    /**
     * @return \Ewave\ProductOverlay\Model\Overlays
     */
    public function getOverlay()
    {
        return $this->_overlay;
    }

    /**
     * Get container path from module settings
     *
     * @return string
     */
    public function getContainerPath()
    {
        return $this->_helper->getContainerPath($this->getOverlay()->getMode());
    }

    /**
     * Get image url withmode and site url
     *
     * @return string
     */
    public function getImageScr()
    {
        $img = $this->_overlay->getValue('img');

        return $this->_helper->getImageUrl($img);
    }
}
