<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Banner\Validation;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Validation\ValidationException;

class ActiveDatesValidator implements ValidatorInterface
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    /**
     * @param BannerInterface $banner
     *
     * @throws ValidationException
     */
    public function validate(BannerInterface $banner): void
    {
        $fromDate = $banner->getStartDate();
        $toDate = $banner->getEndDate();

        if (!empty($toDate)) {
            $now = $this->timezone->date();
            $toDate = $this->timezone->date($toDate);
            $now->setTime(0, 0, 0);

            if ($toDate < $now) {
                throw new ValidationException(__('End date cannot be earlier than current date'));
            }

            if (!empty($fromDate)) {
                $fromDate = $this->timezone->date($fromDate);

                if ($toDate < $fromDate) {
                    throw new ValidationException(__('End date cannot be earlier than start date'));
                }
            }
        }
    }
}
