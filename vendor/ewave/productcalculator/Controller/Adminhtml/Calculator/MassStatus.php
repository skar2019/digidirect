<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml\Calculator;

use Ewave\ProductCalculator\Api\CalculatorRepositoryInterface;
use Ewave\ProductCalculator\Controller\Adminhtml\AbstractMassStatus;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ProductCalculator\Model\ResourceModel\Calculator\CollectionFactory;

/**
 * Class MassStatus
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Calculator
 */
class MassStatus extends AbstractMassStatus
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
    protected function updateStatus($entityIds, $status)
    {
        return $this->calculatorRepository->updateStatus($entityIds, (int)$status);
    }
}
