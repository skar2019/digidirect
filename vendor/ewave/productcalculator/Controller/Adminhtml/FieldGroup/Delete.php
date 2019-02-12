<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup;

use Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup;

/**
 * Class Delete
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup
 */
class Delete extends FieldGroup
{
    /**
     * @var \Ewave\ProductCalculator\Model\FieldGroupRepository
     */
    protected $fieldGroupRepository;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ewave\ProductCalculator\Model\FieldGroupRepository $fieldGroupRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ewave\ProductCalculator\Model\FieldGroupRepository $fieldGroupRepository
    ) {
        $this->fieldGroupRepository = $fieldGroupRepository;
        parent::__construct($context);
    }

    /**
     * Save action
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
                $this->fieldGroupRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted field group.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a field group to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
