<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Test\Unit\Model\Banner\Validation;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\Banner\Validation\CompositeValidator;
use Amasty\BannerSlider\Model\Banner\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @see \Amasty\BannerSlider\Model\Banner\Validation\CompositeValidator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class CompositeValidatorTest extends TestCase
{
    /**
     * @covers \Amasty\BannerSlider\Model\Banner\Validation\CompositeValidator::validate
     */
    public function testValidate(): void
    {
        $banner = $this->getMockForAbstractClass(BannerInterface::class);
        $subValidator = $this->getMockForAbstractClass(ValidatorInterface::class);
        $subValidator->expects($this->once())->method('validate');
        $validator = new CompositeValidator([$subValidator]);
        $validator->validate($banner);
    }
}
