<?php

namespace Ewave\Banner\Helper\Video;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as ContextHelper;
use Ewave\Banner\Helper\Image\Config as ImageConfigHelper;

class Config extends AbstractHelper
{
    const ROLE_DESKTOP_CODE = 'rotator_desktop';
    const ROLE_MOBILE_CODE = 'rotator_mobile';
    const ROLE_DESKTOP_LABEL = 'Desktop';
    const ROLE_MOBILE_LABEL = 'Mobile';
    const ROLE_TABLET_LABEL = 'Tablet';
    const ROLE_TABLET_CODE = 'rotator_tablet';

    /**
     * @var ImageConfigHelper
     */
    protected $imageConfigHelper;

    /**
     * Config constructor.
     *
     * @param ContextHelper $context
     * @param ImageConfigHelper $configHelper
     */
    public function __construct(
        ContextHelper $context,
        ImageConfigHelper $configHelper
    ) {
        parent::__construct($context);
        $this->imageConfigHelper = $configHelper;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return [
            self::ROLE_DESKTOP_CODE => self::ROLE_DESKTOP_LABEL,
            self::ROLE_MOBILE_CODE => self::ROLE_MOBILE_LABEL,
            self::ROLE_TABLET_CODE => self::ROLE_TABLET_LABEL,
        ];
    }

    /**
     * @param bool $withDot
     * @return array
     */
    public function getPreviewImageAllowedExtensions($withDot = false)
    {
        $allowedExtensions = $this->imageConfigHelper->getAllowedExtensions();
        if ($withDot) {
            $allowedExtensions = array_map(
                function ($value) {
                    return '.' . $value;
                },
                $allowedExtensions
            );
        }
        return $allowedExtensions;
    }

    /**
     * @return ImageConfigHelper
     */
    public function getImageConfigHelper()
    {
        return $this->imageConfigHelper;
    }
}
