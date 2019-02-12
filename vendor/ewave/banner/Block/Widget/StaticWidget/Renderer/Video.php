<?php

namespace Ewave\Banner\Block\Widget\StaticWidget\Renderer;

use Ewave\Banner\Block\Widget\Helper\BannerValueGetter;
use Ewave\Banner\Block\Widget\Helper\VideoHelper;
use Ewave\Banner\Block\Widget\StaticWidget\TypeRendererInterface;
use Magento\Framework\View\Element\Template;

class Video extends Renderer implements TypeRendererInterface
{
    const TYPE = 'video';

    /**
     * @var VideoHelper
     */
    protected $videoHelper;

    /**
     * Video constructor.
     *
     * @param Template\Context $context
     * @param VideoHelper $videoHelper
     * @param BannerValueGetter $bannerValueGetter
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        VideoHelper $videoHelper,
        BannerValueGetter $bannerValueGetter,
        array $data = []
    ) {
        $this->videoHelper = $videoHelper;
        parent::__construct($context, $bannerValueGetter, $videoHelper, $data);
    }

    /**
     * @return array
     */
    public function getVideo()
    {
        return $this->videoHelper->get($this->banner);
    }

    /**
     * @return mixed
     */
    public function getMediaQuery()
    {
        return $this->videoHelper->getMediaQuery($this->banner);
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
