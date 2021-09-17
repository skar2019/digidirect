<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Banner;

use Amasty\BannerSlider\Api\Data\BannerInterface;

class MassDelete extends AbstractMassAction
{
    protected function itemAction(BannerInterface $banner)
    {
        $this->repository->delete($banner);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been deleted.');
    }
}
