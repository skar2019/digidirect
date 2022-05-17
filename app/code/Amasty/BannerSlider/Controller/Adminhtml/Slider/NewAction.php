<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Slider;

class NewAction extends Edit
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_BannerSlider::sliders_slider';
}
