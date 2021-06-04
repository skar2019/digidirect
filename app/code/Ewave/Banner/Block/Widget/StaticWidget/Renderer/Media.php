<?php

namespace Ewave\Banner\Block\Widget\StaticWidget\Renderer;

use Ewave\Banner\Block\Widget\Helper\BannerValueGetter;
use Ewave\Banner\Block\Widget\Helper\ImageHelper;
use Ewave\Banner\Block\Widget\Helper\MediaQueryHelper;
use Ewave\Banner\Block\Widget\Helper\VideoHelper;
use Ewave\Banner\Block\Widget\StaticWidget\TypeRendererInterface;
use Ewave\Banner\Helper\IssetTrait;
use Magento\Framework\View\Element\Template;

class Media extends Renderer implements TypeRendererInterface
{
    use IssetTrait;

    const TYPE = 'media';

    /**
     * @var MediaQueryHelper
     */
    protected $mediaQueryHelper;

    /**
     * @var VideoHelper
     */
    protected $videoHelper;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var BannerValueGetter
     */
    protected $bannerValueGetter;

    /**
     * Media constructor.
     *
     * @param Template\Context $context
     * @param MediaQueryHelper $mediaHelper
     * @param ImageHelper $imageHelper
     * @param VideoHelper $videoHelper
     * @param BannerValueGetter $bannerValueGetter
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        MediaQueryHelper $mediaHelper,
        ImageHelper $imageHelper,
        VideoHelper $videoHelper,
        BannerValueGetter $bannerValueGetter,
        array $data = []
    ) {
        $this->videoHelper = $videoHelper;
        $this->imageHelper = $imageHelper;
        $this->mediaQueryHelper = $mediaHelper;
        parent::__construct($context, $bannerValueGetter, $videoHelper, $data);
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->imageHelper->get($this->banner);
    }

    /**
     * @return array
     */
    public function getVideo()
    {
        return $this->videoHelper->get($this->banner);
    }

    /**
     * @return array
     */
    public function getMediaQuery()
    {
        return $this->mediaQueryHelper->get($this->banner);
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
     * @param array $video
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getVideoAttribute(array $video, $key, $default = null)
    {
        return $this->videoHelper->getVideoAttribute($video, $key, $default);
    }
}
