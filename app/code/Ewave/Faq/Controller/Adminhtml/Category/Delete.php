<?php

namespace Ewave\Faq\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Ewave\Faq\Api\CategoryRepositoryInterface;
use Magento\Store\Model\Store;

/**
 * Class Delete
 * @package Ewave\Faq\Controller\Adminhtml\Category
 */
class Delete extends Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Action\Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \Ewave\Faq\Model\Category $category */
                $category = $this->categoryRepository->getById($id);
                if ($category->getId()) {
                    if ($category->canBeDeleted()) {
                        $this->categoryRepository->deleteById($category->getId());
                        $this->messageManager->addSuccessMessage(__('You deleted the faq category item.'));
                    } else {
                        $this->messageManager->addWarningMessage(
                            __('The category can\'t be deleted. Please unassign FAQs and try again.')
                        );
                    }
                } else {
                    $this->messageManager->addWarningMessage(__('Category not found'));
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a faq category item to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check if Is allowed to delete
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_Faq::faq_category_items_delete');
    }
}
