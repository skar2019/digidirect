<?php

namespace Ewave\Banner\Block\Widget\StaticWidget\Renderer;

use Ewave\Banner\Block\Widget\Banner;
use Ewave\Banner\Block\Widget\Helper\BannerValueGetter;
use Ewave\Banner\Block\Widget\Helper\CustomAttributesHelper;
use Ewave\Banner\Block\Widget\Helper\CustomAttributesHelperInterface;
use Ewave\Banner\Block\Widget\Helper\ImageHelper;
use Ewave\Banner\Block\Widget\Helper\MediaHelperInterface;
use Ewave\Banner\Block\Widget\Helper\MediaQueryHelper;
use Ewave\Banner\Block\Widget\Helper\VideoHelper;
use Ewave\Banner\Block\Widget\StaticWidget\TypeRendererInterface;
use Magento\Framework\View\Element\Template;

class Renderer extends Template implements TypeRendererInterface
{
    /**
     * @var Banner
     */
    protected $widget;

    /**
     * @var []
     */
    protected $banner;

    /**
     * @var BannerValueGetter
     */
    protected $bannerValueGetter;

    /**
     * @var CustomAttributesHelper
     */
    protected $customAttributesHelper;

    /**
     * Renderer constructor.
     *
     * @param Template\Context $context
     * @param BannerValueGetter $bannerValueGetter
     * @param CustomAttributesHelperInterface $customAttributesHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BannerValueGetter $bannerValueGetter,
        CustomAttributesHelperInterface $customAttributesHelper,
        array $data = []
    ) {
        $this->customAttributesHelper = $customAttributesHelper;
        $this->bannerValueGetter = $bannerValueGetter;
        parent::__construct($context, $data);
    }

    /**
     * @param Banner $bannerWidget
     * @return TypeRendererInterface
     */
    public function setWidget(Banner $bannerWidget)
    {
        $this->widget = $bannerWidget;
        return $this;
    }

    /**
     * @param array $banner
     * @return TypeRendererInterface
     */
    public function setBanner(array $banner = [])
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * @return string
     */
    public function renderBanner()
    {
        return (string)$this->toHtml();
    }

    /**
     * @return Banner
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * @return array
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @param string $template
     * @return TypeRendererInterface
     */
    public function setBannerTemplate($template)
    {
        $this->setTemplate($template);
        return $this;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getRootSetting($key, $default = null)
    {
        return $this->bannerValueGetter->get($this->banner, $key, $default);
    }

    /**
     * @param string $string
     * @param null $default
     * @return mixed|null
     */
    public function getCustomSetting($string, $default = null)
    {
        return $this->customAttributesHelper->getCustomSetting($this->banner, $string, $default);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return (string)$this->getRootSetting('content');
    }
}
