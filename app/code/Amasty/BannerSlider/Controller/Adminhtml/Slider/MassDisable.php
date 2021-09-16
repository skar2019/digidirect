<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Slider;

use Amasty\BannerSlider\Api\Data\SliderInterface;

class MassDisable extends AbstractMassAction
{
    protected function itemAction(SliderInterface $slider)
    {
        $this->repository->disable($slider);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t disable item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been disabled.', $collectionSize);
        }

        return __('No records have been disabled.');
    }
}
