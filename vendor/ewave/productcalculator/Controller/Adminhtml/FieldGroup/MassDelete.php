<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup;

use Ewave\ProductCalculator\Api\FieldGroupRepositoryInterface;
use Ewave\ProductCalculator\Controller\Adminhtml\AbstractMassAction;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroup\CollectionFactory;

/**
 * Class MassDelete
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup
 */
class MassDelete extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Ewave_ProductCalculator::field_group_manage';

    /**
     * @var FieldGroupRepositoryInterface
     */
    protected $fieldGroupRepository;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FieldGroupRepositoryInterface $fieldGroupRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FieldGroupRepositoryInterface $fieldGroupRepository
    ) {
        parent::__construct($context, $filter);
        $this->fieldGroupRepository = $fieldGroupRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function doAction($entityIds, $params = null)
    {
        $itemsDeleted = 0;
        foreach ($entityIds as $entityId) {
            $this->fieldGroupRepository->deleteById($entityId);
            $itemsDeleted++;
        }
        return $itemsDeleted;
    }

    /**
     * {@inheritdoc}
     */
    protected function addSuccessMessage($recordsAffected)
    {
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $recordsAffected));
    }
}
