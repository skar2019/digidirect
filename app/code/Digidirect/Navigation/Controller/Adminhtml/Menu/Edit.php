<?php

namespace Digidirect\Navigation\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Digidirect\Navigation\Model\MenuFactory;
use Digidirect\Navigation\Model\Registry\Constants;
use Digidirect\Navigation\Model\ResourceModel\MenuRepository;

/**
 * Class Edit
 * @package Digidirect\Navigation\Controller\Adminhtml\Menu
 */
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
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * NewAction constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param MenuFactory $menuFactory
     * @param MenuRepository $menuRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        MenuFactory $menuFactory,
        MenuRepository $menuRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->menuFactory = $menuFactory;
        $this->menuRepository = $menuRepository;
        parent::__construct($context);
    }

    /**
     * Menu Edit From
     *
     * @return \Magento\Framework\View\Result\Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $request = $this->getRequest();
        $id = (int)$request->getParam('id', 0);
        $storeId = (int)$request->getParam('store', 0);

        /**
         * @var $model \Digidirect\Navigation\Model\Menu
         */
        $model = $this->menuFactory->create();

        if ($id) {
            $model = $this->menuRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This menu item doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $model->setCurrentStoreId($storeId);
        $this->registry->register(Constants::CURRENT_MENU_ITEM, $model);
        $this->registry->register(Constants::CURRENT_STORE_ID, $storeId);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Digidirect_Navigation::navigation')
            ->addBreadcrumb(__('Digidirect Navigation'), __('Digidirect Navigation'))
            ->addBreadcrumb(
                $id ? __('Edit Menu Item') : __('New Menu Item'),
                $id ? __('Edit Menu Item') : __('New Menu Item')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Menu Management'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Menu Item'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digidirect_Navigation::navigation');
    }
}
