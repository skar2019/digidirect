<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup;

use Ewave\ProductCalculator\Model\Constants;
use Ewave\ProductCalculator\Model\FieldGroupRepository;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup
 */
class Edit extends \Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup
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
     * @var FieldGroupRepository
     */
    protected $fieldGroupRepository;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldGroupFactory
     */
    protected $fieldGroupFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param FieldGroupRepository $fieldGroupRepository
     * @param \Ewave\ProductCalculator\Model\FieldGroupFactory $fieldGroupFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        FieldGroupRepository $fieldGroupRepository,
        \Ewave\ProductCalculator\Model\FieldGroupFactory $fieldGroupFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->fieldGroupRepository = $fieldGroupRepository;
        $this->fieldGroupFactory = $fieldGroupFactory;
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
        $model = $this->fieldGroupFactory->create();

        if ($id) {
            try {
                $model = $this->fieldGroupRepository->getById($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register(Constants::CURRENT_FIELD_GROUP, $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit User Input Field Group') : __('New User Input Field Group'),
            $id ? __('Edit User Input Field Group') : __('New User Input Field Group')
        );
        $resultPage->getConfig()->getTitle()->prepend(__(' User Input Field Groups'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New User Input Field Group'));
        return $resultPage;
    }
}
