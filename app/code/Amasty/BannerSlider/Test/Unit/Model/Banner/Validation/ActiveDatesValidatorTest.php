<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Test\Unit\Model\Banner\Validation;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\Banner\Validation\ActiveDatesValidator;
use Amasty\BannerSlider\Test\Unit\Traits\ObjectManagerTrait;
use Amasty\BannerSlider\Test\Unit\Traits\ReflectionTrait;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @see \Amasty\BannerSlider\Model\Banner\Validation\ActiveDatesValidator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ActiveDatesValidatorTest extends TestCase
{
    use ObjectManagerTrait;
    use ReflectionTrait;

    /**
     * @covers \Amasty\BannerSlider\Model\Banner\Validation\ActiveDatesValidator::validate
     * @dataProvider getTestData
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $nowDate
     */
    public function testValidate(string $startDate, string $endDate, string $nowDate)
    {
        $timezone = $this->getMockForAbstractClass(TimezoneInterface::class);
        $dateMethodConfig = $timezone->expects($this->any())->method('date');
        $dateMethodConfig->willReturnCallback(function (?string $date) use ($nowDate) {
            $date = $date ?: $nowDate;

            return new \DateTime($date);
        });
        $objectManager = $this->getObjectManager();
        $validator = $objectManager->getObject(
            ActiveDatesValidator::class,
            [
                'timezone' => $timezone
            ]
        );
        $banner = $this->getMockForAbstractClass(BannerInterface::class);
        $banner->expects($this->once())->method('getStartDate')->willReturn($startDate);
        $banner->expects($this->once())->method('getEndDate')->willReturn($endDate);
        $this->expectException(ValidationException::class);

        $validator->validate($banner);
    }

    public function getTestData(): array
    {
        return [
            [
                '2021-06-24',
                '2021-06-28',
                '2021-06-29'
            ],
            [
                '2021-06-28',
                '2021-06-24',
                '2021-06-25'
            ],
        ];
    }
}
