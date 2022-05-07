<?php

namespace Marketplacer\Brand\Controller\Adminhtml\Brand;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Throwable;

/**
 * Class Save
 * @package Marketplacer\Brand\Controller\Adminhtml\Brand
 */
class Delete extends AbstractBrandAction
{
    /**
     * Save action
     * @return ResultInterface
     */
    public function execute()
    {
        if (!$this->isAdminEditAllowed()) {
            return $this->processEditNotAllowedRedirect();
        }

        $brandId = $this->getRequest()->getParam('brand_id');

        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->brandRepository->deleteById($brandId);
            $this->cacheInvalidator->invalidate();
            $this->messageManager->addSuccessMessage(__('Brand successfully deleted [ID: "%1"]', $brandId));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The brand with ID "%1" does not exist.', $brandId ?? ''));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index');
        } catch (Throwable $e) {
            $this->messageManager->addErrorMessage(__('An error occurred by deleting of brand with ID "%1"',
                $brandId ?? ''));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index');
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
