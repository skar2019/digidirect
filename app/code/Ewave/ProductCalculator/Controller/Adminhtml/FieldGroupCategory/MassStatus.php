<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory;

use Ewave\ProductCalculator\Api\FieldGroupCategoryRepositoryInterface;
use Ewave\ProductCalculator\Controller\Adminhtml\AbstractMassStatus;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroupCategory\CollectionFactory;

/**
 * Class MassStatus
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory
 */
class MassStatus extends AbstractMassStatus
{
    const ADMIN_RESOURCE = 'Ewave_ProductCalculator::field_group_category_manage';

    /**
     * @var FieldGroupCategoryRepositoryInterface
     */
    protected $fieldGroupCategoryRepository;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FieldGroupCategoryRepositoryInterface $fieldGroupCategoryRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FieldGroupCategoryRepositoryInterface $fieldGroupCategoryRepository
    ) {
        parent::__construct($context, $filter);
        $this->fieldGroupCategoryRepository = $fieldGroupCategoryRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function updateStatus($entityIds, $status)
    {
        return $this->fieldGroupCategoryRepository->updateStatus($entityIds, (int)$status);
    }
}
