<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Banner\Validation;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\Validation\ValidationException;

interface ValidatorInterface
{
    /**
     * @param BannerInterface $banner
     *
     * @throws ValidationException
     */
    public function validate(BannerInterface $banner): void;
}
