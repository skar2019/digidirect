<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Banner\Validation;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\Validation\ValidationException;

class CompositeValidator implements ValidatorInterface
{
    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    public function __construct(
        array $validators
    ) {
        $this->validators = $validators;
    }

    /**
     * @param BannerInterface $banner
     *
     * @throws ValidationException
     */
    public function validate(BannerInterface $banner): void
    {
        foreach ($this->validators as $validator) {
            if ($validator instanceof ValidatorInterface) {
                $validator->validate($banner);
            }
        }
    }
}
