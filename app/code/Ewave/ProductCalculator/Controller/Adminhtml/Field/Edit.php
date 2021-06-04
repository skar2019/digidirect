<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\Field;

use Ewave\ProductCalculator\Model\Constants;
use Ewave\ProductCalculator\Model\FieldRepository;
use Ewave\ProductCalculator\Model\CalculatorRepository;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Field
 */
class Edit extends \Ewave\ProductCalculator\Controller\Adminhtml\Field
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
     * @var FieldRepository
     */
    protected $fieldRepository;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var CalculatorRepository
     */
    protected $calculatorRepository;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param FieldRepository $fieldRepository
     * @param CalculatorRepository $calculatorRepository
     * @param \Ewave\ProductCalculator\Model\FieldFactory $fieldFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        FieldRepository $fieldRepository,
        CalculatorRepository $calculatorRepository,
        \Ewave\ProductCalculator\Model\FieldFactory $fieldFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->fieldRepository = $fieldRepository;
        $this->calculatorRepository = $calculatorRepository;
        $this->fieldFactory = $fieldFactory;
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
        $model = $this->fieldFactory->create();

        if ($id) {
            try {
                $model = $this->fieldRepository->getById($id);
                if ($calculatorId = $model->getCalculatorId()) {
                    $calculator = $this->calculatorRepository->getById($calculatorId);
                    $this->coreRegistry->register(Constants::CURRENT_CALCULATOR, $calculator);
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register(Constants::CURRENT_FIELD, $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit User Input Field') : __('New User Input Field'),
            $id ? __('Edit User Input Field') : __('New User Input Field')
        );
        $resultPage->getConfig()->getTitle()->prepend(__(' User Input Fields'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New User Input Field'));
        return $resultPage;
    }
}
