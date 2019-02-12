<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\Calculator;

use Ewave\ProductCalculator\Controller\Adminhtml\Calculator;

/**
 * Class Delete
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Calculator
 */
class Delete extends Calculator
{
    /**
     * @var \Ewave\ProductCalculator\Model\CalculatorRepository
     */
    protected $calculatorRepository;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ewave\ProductCalculator\Model\CalculatorRepository $calculatorRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ewave\ProductCalculator\Model\CalculatorRepository $calculatorRepository
    ) {
        $this->calculatorRepository = $calculatorRepository;
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
                $this->calculatorRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted calculator.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a calculator to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
