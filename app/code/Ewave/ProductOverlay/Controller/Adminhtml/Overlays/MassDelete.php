<?php

namespace Ewave\ProductOverlay\Controller\Adminhtml\Overlays;

use Ewave\ProductOverlay\Model\Overlays;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 * @package Ewave\ProductOverlay\Controller\Adminhtml\Overlays
 */
class MassDelete extends \Ewave\ProductOverlay\Controller\Adminhtml\Overlays
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $overlayDeleted = 0;

        /** @var Overlays $overlay */
        foreach ($collection->getItems() as $overlay) {
            $this->_overlayRepository->deleteById($overlay->getId());
            $overlayDeleted++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $overlayDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('ewave_productoverlay/*/index');
    }
}
