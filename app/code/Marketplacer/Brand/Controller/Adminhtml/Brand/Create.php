<?php

namespace Marketplacer\Brand\Controller\Adminhtml\Brand;

use Magento\Framework\Controller\ResultInterface;

/**
 * Class Create
 * @package Marketplacer\Brand\Controller\Adminhtml\Brand
 */
class Create extends AbstractBrandAction
{
    /**
     * Create brand form
     *
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if (!$this->isAdminEditAllowed()) {
            return $this->processEditNotAllowedRedirect();
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Brands'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Brand'));

        return $resultPage;
    }
}
