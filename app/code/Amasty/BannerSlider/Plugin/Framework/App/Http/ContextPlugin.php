<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Plugin\Framework\App\Http;

use Amasty\BannerSlider\Model\MobileDetect;
use Magento\Framework\App\Http\Context as HttpContext;

class ContextPlugin
{
    const IS_MOBILE_HTTP_CONTEXT_KEY  = 'IS_MOBILE_HTTP_CONTEXT_KEY';

    /**
     * @var MobileDetect
     */
    private $mobileDetect;

    public function __construct(
        MobileDetect $mobileDetect
    ) {
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * @param HttpContext $subject
     */
    public function beforeGetVaryString(HttpContext $subject)
    {
        $subject->setValue(self::IS_MOBILE_HTTP_CONTEXT_KEY, (int)$this->mobileDetect->isMobile(), '');
    }
}
