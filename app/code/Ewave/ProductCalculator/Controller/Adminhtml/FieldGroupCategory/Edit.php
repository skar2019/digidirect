<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory;

use Ewave\ProductCalculator\Model\Constants;
use Ewave\ProductCalculator\Model\FieldGroupCategoryRepository;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory
 */
class Edit extends \Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory
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
     * @var FieldGroupCategoryRepository
     */
    protected $fieldGroupCategoryRepository;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldGroupCategoryFactory
     */
    protected $fieldGroupCategoryFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param FieldGroupCategoryRepository $fieldGroupCategoryRepository
     * @param \Ewave\ProductCalculator\Model\FieldGroupCategoryFactory $fieldGroupCategoryFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        FieldGroupCategoryRepository $fieldGroupCategoryRepository,
        \Ewave\ProductCalculator\Model\FieldGroupCategoryFactory $fieldGroupCategoryFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->fieldGroupCategoryRepository = $fieldGroupCategoryRepository;
        $this->fieldGroupCategoryFactory = $fieldGroupCategoryFactory;
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
        $model = $this->fieldGroupCategoryFactory->create();

        if ($id) {
            try {
                $model = $this->fieldGroupCategoryRepository->getById($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register(Constants::CURRENT_FIELD_GROUP_CATEGORY, $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Field Group Category') : __('New Field Group Category'),
            $id ? __('Edit Field Group Category') : __('New Field Group Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Field Group Categories'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Field Group Category'));
        return $resultPage;
    }
}
