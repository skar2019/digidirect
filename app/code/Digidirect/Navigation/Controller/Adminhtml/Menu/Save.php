<?php

namespace Digidirect\Navigation\Controller\Adminhtml\Menu;

use Digidirect\Navigation\Constants\Acl;
use Magento\Backend\App\Action;
use Digidirect\Navigation\Api\MenuRepositoryInterface;
use Digidirect\Navigation\Model\MenuFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Digidirect\Navigation\Helper\Data as NavigationHelper;
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * Class Save
 * @package Digidirect\Navigation\Controller\Adminhtml\Menu
 */
class Save extends Action
{
    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    /**
     * @var NavigationHelper
     */
    protected $navigationHelper;

    /**
     * @var array
     */
    protected $data;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param MenuRepositoryInterface $menuRepository
     * @param MenuFactory $menu
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param NavigationHelper $navigationHelper
     * @param [] $data
     */
    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository,
        MenuFactory $menu,
        TypeListInterface $typeList,
        NavigationHelper $navigationHelper,
        array $data = []
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->menuFactory = $menu;
        $this->typeList = $typeList;
        $this->navigationHelper = $navigationHelper;
        $this->data = $data;
    }

    /**
     * Save Menu Item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('entity_id', 0);
        $storeId = $this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);
        $params = $this->getRequest()->getPostValue();
        $customOptions = $this->getRequest()->getParam('custom_options_grid', []);
        $params['custom_options'] = $customOptions;

        if ($params) {
            try {
                $menuModel = $this->menuFactory->create();
                if ($id > 0) {
                    $menuModel = $this->menuRepository->getById($id);
                } else {
                    $params['entity_id'] = null;
                }

                if (!$menuModel->getId() && $id) {
                    throw new LocalizedException(__('This menu item no longer exists.'));
                }

                $params = $this->_prepareData($params);
                $menuModel->setData($params);
                $menuModel->serializeCustomOptions();
                $stores = $menuModel->getId() ? [$storeId] : [Store::DEFAULT_STORE_ID, $storeId];
                $menuModel->setStores($stores);

                $this->_eventManager->dispatch(
                    'menu_item_prepare_save',
                    ['menu' => $menuModel, 'request' => $this->getRequest()]
                );

                $this->menuRepository->save($menuModel);

                $this->messageManager->addSuccessMessage(__('You saved menu item'));

                $this->typeList->invalidate($this->data['invalidate_cache_types'] ?? []);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $menuModel->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check permissions for this action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(Acl::ACL_DIGIDIRECT_NAVIGATION_MENU_ITEM_SAVE);
    }

    /**
     * Prepare data for save
     *
     * @param array $data
     * @return array
     */
    protected function _prepareData($data)
    {
        if (isset($data['link']) && $this->navigationHelper->hasPhonePrefix($data['link'])) {
            $data['link'] = $this->navigationHelper->removeSpaces($data['link']);
        }
        return $data;
    }
}
