<?php

namespace Ewave\Navigation\Controller\Adminhtml\Set;

use Ewave\Navigation\Api\SetRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\Navigation\Model\ResourceModel\Set\CollectionFactory;

/**
 * Class MassStatus
 * @package Ewave\Navigation\Controller\Adminhtml\Set
 */
class MassStatus extends \Ewave\Navigation\Controller\Adminhtml\AbstractMassAction
{
    const ADMIN_RESOURCE = 'Ewave_Navigation::navigation_menu_sets_save';

    /**
     * @var SetRepositoryInterface
     */
    protected $setRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param SetRepositoryInterface $setRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        SetRepositoryInterface $setRepository
    ) {
        parent::__construct($context, $filter);
        $this->setRepository = $setRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Mass change status action
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $status = $this->getRequest()->getParam('status');
        $recordsUpdated = 0;
        foreach ($collection->getAllIds() as $setId) {
            $set = $this->setRepository->getById($setId);
            $set->setStatus($status);
            $set->setStoreId($set->getStoreIds());
            $this->setRepository->save($set);
            $recordsUpdated++;
        }

        if ($recordsUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $recordsUpdated));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
