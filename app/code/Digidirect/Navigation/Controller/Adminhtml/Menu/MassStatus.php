<?php

namespace Digidirect\Navigation\Controller\Adminhtml\Menu;

use Digidirect\Navigation\Api\MenuRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Digidirect\Navigation\Model\ResourceModel\Menu\CollectionFactory;

/**
 * Class MassStatus
 * @package Digidirect\Navigation\Controller\Adminhtml\Menu
 */
class MassStatus extends \Digidirect\Navigation\Controller\Adminhtml\AbstractMassAction
{
    const ADMIN_RESOURCE = 'Digidirect_Navigation::navigation_menu_items_save';

    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param MenuRepositoryInterface $menuRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        MenuRepositoryInterface $menuRepository
    ) {
        parent::__construct($context, $filter);
        $this->menuRepository = $menuRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Process mass status change
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $status = $this->getRequest()->getParam('status');

        $recordsUpdated = $this->menuRepository->updateStatus($collection->getAllIds(), (int)$status);

        if ($recordsUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $recordsUpdated));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
