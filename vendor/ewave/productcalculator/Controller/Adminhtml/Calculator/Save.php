<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\Calculator;

use Ewave\ProductCalculator\Controller\Adminhtml\Calculator;
use Ewave\ProductCalculator\Ui\DataProvider\Calculator\Form\DataProvider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Calculator
 */
class Save extends Calculator
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Ewave\ProductCalculator\Model\CalculatorRepository
     */
    protected $calculatorRepository;

    /**
     * @var \Ewave\ProductCalculator\Model\CalculatorFactory
     */
    protected $calculatorFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Ewave\ProductCalculator\Model\CalculatorRepository $calculatorRepository
     * @param \Ewave\ProductCalculator\Model\CalculatorFactory $calculatorFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Ewave\ProductCalculator\Model\CalculatorRepository $calculatorRepository,
        \Ewave\ProductCalculator\Model\CalculatorFactory $calculatorFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->calculatorRepository = $calculatorRepository;
        $this->calculatorFactory = $calculatorFactory;
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
        $editPageRedirect = $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->calculatorFactory->create();
            if ($id) {
                $model = $this->calculatorRepository->getById($id);
            }
            $model->setData($data);
            try {
                $this->calculatorRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the calculator.'));
                $this->dataPersistor->clear(DataProvider::DATA_PERSISTOR_KEY);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the calculator.'));
            }

            $this->dataPersistor->set(DataProvider::DATA_PERSISTOR_KEY, $data);
            return $editPageRedirect;
        }
        return $resultRedirect->setPath('*/*/');
    }
}
