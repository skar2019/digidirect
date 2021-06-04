<?php
namespace Ewave\Faq\Controller\Adminhtml\Category;

use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ewave\Faq\Model\CategoryFactory;
use Ewave\Faq\Model\ResourceModel\CategoryRepository;
use Ewave\Faq\Model\Registry\Constants;

/**
 * Class Edit
 * @package Ewave\Faq\Controller\Adminhtml\Category
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var  CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepository $categoryRepository
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        CategoryFactory $categoryFactory,
        CategoryRepository $categoryRepository
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->registry             = $registry;
        $this->categoryFactory      = $categoryFactory;
        $this->categoryRepository   = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $request = $this->getRequest();
        $id = (int)$request->getParam('id', 0);

        /**
         * @var $model \Ewave\Faq\Model\Category
         */
        $model = $this->categoryFactory->create();

        if ($id) {
            $model = $this->categoryRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This category item doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->registry->register(Constants::CURRENT_CATEGORY_ITEM, $model);
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ewave_Faq::faq_category')
            ->addBreadcrumb(__('Ewave Faq Category'), __('Ewave Faq Category'))
            ->addBreadcrumb(
                $id ? __('Edit Category Item') : __('New Category Item'),
                $id ? __('Edit Category Item') : __('New Category Item')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Faq Management'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? 'Edit Category' : __('New Faq Category Item'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_Faq::category');
    }
}
