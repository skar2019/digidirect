<?php

namespace Marketplacer\Brand\Controller\Adminhtml\Brand;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class View
 * @package Marketplacer\Brand\Controller\Adminhtml\Brand
 */
class View extends AbstractBrandAction
{
    /**
     * Edit brand
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $brandId = $this->getRequest()->getParam('brand_id');
        try {
            $this->brandRepository->getById($brandId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The brand with ID "%1" does not exist.', $brandId ?? ''));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Brands'));
        $resultPage->getConfig()->getTitle()->prepend(__('View Brand'));

        return $resultPage;
    }
}
