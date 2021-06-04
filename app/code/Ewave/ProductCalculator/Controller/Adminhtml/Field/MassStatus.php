<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml\Field;

use Ewave\ProductCalculator\Api\FieldRepositoryInterface;
use Ewave\ProductCalculator\Controller\Adminhtml\AbstractMassStatus;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ProductCalculator\Model\ResourceModel\Field\CollectionFactory;

/**
 * Class MassStatus
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Field
 */
class MassStatus extends AbstractMassStatus
{
    const ADMIN_RESOURCE = 'Ewave_ProductCalculator::field_manage';

    /**
     * @var FieldRepositoryInterface
     */
    protected $fieldRepository;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FieldRepositoryInterface $fieldRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FieldRepositoryInterface $fieldRepository
    ) {
        parent::__construct($context, $filter);
        $this->fieldRepository = $fieldRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function updateStatus($entityIds, $status)
    {
        return $this->fieldRepository->updateStatus($entityIds, (int)$status);
    }
}
