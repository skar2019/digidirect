<?php
namespace Ewave\NavigationCMSUpgrade\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use Ewave\NavigationCMSUpgrade\Model\Entity\MenuItem;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\Navigation\Model\ResourceModel\Menu\CollectionFactory;

/**
 * Class Generate
 *
 * @package Ewave\NavigationCMSUpgrade\Controller\Adminhtml\Menu
 */
class Generate extends \Ewave\CmsUpgrade\Controller\Adminhtml\Generate
{
    /**
     * @var MenuItem
     */
    protected $menu;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Generate constructor.
     *
     * @param Context $context
     * @param MenuItem $menuItem
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        MenuItem $menuItem,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->menu = $menuItem;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /**
         * @var $menuCollection \Ewave\Navigation\Model\ResourceModel\Menu\Collection
         */
        $menuCollection = $this->menu->getCollection();
        $collection = $this->filter->getCollection($menuCollection);
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$collection->getSize()) {
            $this->messageManager->addWarningMessage(__('Please select items to generate'));
            return $resultRedirect->setRefererOrBaseUrl();
        }

        $this->menu->setParams($collection);
        try {
            $result = $this->menu->generate();
            $this->setGenerateResult($result);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
