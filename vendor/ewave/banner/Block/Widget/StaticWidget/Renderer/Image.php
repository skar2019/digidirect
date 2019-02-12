<?php

namespace Ewave\Banner\Block\Widget\StaticWidget\Renderer;

use Ewave\Banner\Block\Widget\Helper\BannerValueGetter;
use Ewave\Banner\Block\Widget\Helper\ImageHelper;
use Ewave\Banner\Block\Widget\StaticWidget\TypeRendererInterface;
use Ewave\Banner\Helper\IssetTrait;
use Magento\Framework\View\Element\Template;

class Image extends Renderer implements TypeRendererInterface
{
    use IssetTrait;

    const TYPE = 'image';

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * Image constructor.
     *
     * @param Template\Context $context
     * @param ImageHelper $imageHelper
     * @param BannerValueGetter $bannerValueGetter
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ImageHelper $imageHelper,
        BannerValueGetter $bannerValueGetter,
        array $data = []
    ) {
        $this->bannerValueGetter = $bannerValueGetter;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $bannerValueGetter, $imageHelper, $data);
    }

    /**
     * @return mixed
     */
    public function getMediaQuery()
    {
        return $this->imageHelper->getMediaQuery($this->banner);
    }

    /**
     * @param array $image
     * @param string $code
     * @param null $default
     * @return mixed|null
     */
    public function getImageAttribute(array $image, $code, $default = null)
    {
        return $this->imageHelper->getImageAttribute($image, $code, $default);
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->imageHelper->get($this->banner);
    }
}
