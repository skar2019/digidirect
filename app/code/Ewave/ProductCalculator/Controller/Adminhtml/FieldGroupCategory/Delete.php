<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory;

use Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory;

/**
 * Class Delete
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory
 */
class Delete extends FieldGroupCategory
{
    /**
     * @var \Ewave\ProductCalculator\Model\FieldGroupCategoryRepository
     */
    protected $fieldGroupCategoryRepository;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ewave\ProductCalculator\Model\FieldGroupCategoryRepository $fieldGroupCategoryRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ewave\ProductCalculator\Model\FieldGroupCategoryRepository $fieldGroupCategoryRepository
    ) {
        $this->fieldGroupCategoryRepository = $fieldGroupCategoryRepository;
        parent::__construct($context);
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
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->fieldGroupCategoryRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted field group category.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a field group category to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
