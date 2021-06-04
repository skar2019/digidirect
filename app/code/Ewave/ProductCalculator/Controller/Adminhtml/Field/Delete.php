<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\Field;

use Ewave\ProductCalculator\Controller\Adminhtml\Field;

/**
 * Class Delete
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Field
 */
class Delete extends Field
{
    /**
     * @var \Ewave\ProductCalculator\Model\FieldRepository
     */
    protected $fieldRepository;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ewave\ProductCalculator\Model\FieldRepository $fieldRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ewave\ProductCalculator\Model\FieldRepository $fieldRepository
    ) {
        $this->fieldRepository = $fieldRepository;
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
                $this->fieldRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted field.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a field to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
