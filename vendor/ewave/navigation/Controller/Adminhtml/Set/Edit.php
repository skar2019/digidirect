<?php

namespace Ewave\Navigation\Controller\Adminhtml\Set;

use Ewave\Navigation\Api\SetRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Ewave\Navigation\Model\SetFactory;
use Ewave\Navigation\Constants\Acl;

class Edit extends Action
{
    /**
     * @var PageFactory $_resultPageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry $_registry
     */
    protected $registry;

    /**
     * @var SetFactory
     */
    protected $setFactory;

    /**
     * @var SetRepositoryInterface
     */
    protected $setRepository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param SetFactory $setFactory
     * @param SetRepositoryInterface $repository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        SetFactory $setFactory,
        SetRepositoryInterface $repository

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->setFactory = $setFactory;
        $this->setRepository = $repository;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\View\Result\Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('set_id');

        /**
         * @var $model \Ewave\Navigation\Model\Set
         */

        if ($id) {
            $model = $this->setRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This navigation item doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $model = $this->setFactory->create();
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->registry->register('current_navigation_set', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ewave_Navigation::navigation')
            ->addBreadcrumb(__('Ewave Navigation'), __('Ewave Navigation'))
            ->addBreadcrumb(
                $id ? __('Edit Navigation Item') : __('New Navigation Set'),
                $id ? __('Edit Navigation Item') : __('New Navigation Set')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Ewave Navigation Management'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Navigation Set'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(Acl::ACL_EWAVE_NAVIGATION_MENU_SETS);
    }
}
