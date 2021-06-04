<?php
namespace Ewave\Blog\Controller\Adminhtml\Category;

use Ewave\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ewave\Blog\Model\CategoryFactory;
use Ewave\Blog\Model\CategoryRepository;

/**
 * Class Edit
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
     * @var CategoryFactory
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
         * @var $model \Ewave\Blog\Model\Category
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
        $this->registry->register(CategoryInterface::CURRENT_ITEM, $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ewave_Blog::blogcat')
            ->addBreadcrumb(__('Ewave Category Item'), __('Ewave Category Item'))
            ->addBreadcrumb(
                $id ? __('Edit Category Item') : __('Edit Category Item'),
                $id ? __('Edit Category Item') : __('New Category')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Category Management'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? 'Edit Category Item' : __('New Category'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_Blog::blogcat');
    }
}
