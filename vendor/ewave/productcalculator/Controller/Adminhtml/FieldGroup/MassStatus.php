<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup;

use Ewave\ProductCalculator\Api\FieldGroupRepositoryInterface;
use Ewave\ProductCalculator\Controller\Adminhtml\AbstractMassStatus;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroup\CollectionFactory;

/**
 * Class MassStatus
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup
 */
class MassStatus extends AbstractMassStatus
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
    protected function updateStatus($entityIds, $status)
    {
        return $this->fieldGroupRepository->updateStatus($entityIds, (int)$status);
    }
}
