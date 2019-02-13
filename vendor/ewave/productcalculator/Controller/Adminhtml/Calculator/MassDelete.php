<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml\Calculator;

use Ewave\ProductCalculator\Api\CalculatorRepositoryInterface;
use Ewave\ProductCalculator\Controller\Adminhtml\AbstractMassAction;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ProductCalculator\Model\ResourceModel\Calculator\CollectionFactory;

/**
 * Class MassDelete
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Calculator
 */
class MassDelete extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Ewave_ProductCalculator::calculator_manage';

    /**
     * @var CalculatorRepositoryInterface
     */
    protected $calculatorRepository;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CalculatorRepositoryInterface $calculatorRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CalculatorRepositoryInterface $calculatorRepository
    ) {
        parent::__construct($context, $filter);
        $this->calculatorRepository = $calculatorRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function doAction($entityIds, $params = null)
    {
        $itemsDeleted = 0;
        foreach ($entityIds as $entityId) {
            $this->calculatorRepository->deleteById($entityId);
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
