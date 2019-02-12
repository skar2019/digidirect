<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\Calculator;

use Ewave\ProductCalculator\Model\Constants;
use Ewave\ProductCalculator\Model\CalculatorRepository;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Calculator
 */
class Edit extends \Ewave\ProductCalculator\Controller\Adminhtml\Calculator
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var CalculatorRepository
     */
    protected $calculatorRepository;

    /**
     * @var \Ewave\ProductCalculator\Model\CalculatorFactory
     */
    protected $calculatorFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CalculatorRepository $calculatorRepository
     * @param \Ewave\ProductCalculator\Model\CalculatorFactory $calculatorFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CalculatorRepository $calculatorRepository,
        \Ewave\ProductCalculator\Model\CalculatorFactory $calculatorFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->calculatorRepository = $calculatorRepository;
        $this->calculatorFactory = $calculatorFactory;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->calculatorFactory->create();

        if ($id) {
            try {
                $model = $this->calculatorRepository->getById($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register(Constants::CURRENT_CALCULATOR, $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Calculator') : __('New Calculator'),
            $id ? __('Edit Calculator') : __('New Calculator')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Calculators'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Calculator'));
        return $resultPage;
    }
}
