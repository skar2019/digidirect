<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml\Field;

use Ewave\ProductCalculator\Api\FieldRepositoryInterface;
use Ewave\ProductCalculator\Controller\Adminhtml\AbstractMassAction;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ProductCalculator\Model\ResourceModel\Field\CollectionFactory;

/**
 * Class MassDelete
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Field
 */
class MassDelete extends AbstractMassAction
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
    protected function doAction($entityIds, $params = null)
    {
        $itemsDeleted = 0;
        foreach ($entityIds as $entityId) {
            $this->fieldRepository->deleteById($entityId);
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
