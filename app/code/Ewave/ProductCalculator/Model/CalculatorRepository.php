<?php

namespace Ewave\ProductCalculator\Model;

use Ewave\ProductCalculator\Api\Data\CalculatorInterface;
use Ewave\ProductCalculator\Api\CalculatorRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CalculatorRepository implements CalculatorRepositoryInterface
{
    /**
     * @var CalculatorFactory
     */
    protected $calculatorFactory;

    /**
     * @var ResourceModel\Calculator
     */
    protected $calculatorResource;

    /**
     * CalculatorRepository constructor.
     * @param CalculatorFactory $calculatorFactory
     * @param ResourceModel\Calculator $calculatorResource
     */
    public function __construct(
        CalculatorFactory $calculatorFactory,
        \Ewave\ProductCalculator\Model\ResourceModel\Calculator $calculatorResource
    ) {
        $this->calculatorFactory = $calculatorFactory;
        $this->calculatorResource = $calculatorResource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CalculatorInterface $calculator)
    {
        try {
            $this->prepareData($calculator);
            $calculator->getResource()->save($calculator);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save product finder calculator: %1',
                $exception->getMessage()
            ));
        }
        return $calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($calculatorId)
    {
        $calculator = $this->calculatorFactory->create();
        $calculator->getResource()->load($calculator, $calculatorId);
        if (!$calculator->getId()) {
            throw new NoSuchEntityException(__('Product Finder with id "%1" does not exist.', $calculatorId));
        }
        return $calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CalculatorInterface $calculator)
    {
        try {
            $calculator->getResource()->delete($calculator);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete product finder: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($calculatorId)
    {
        return $this->delete($this->getById($calculatorId));
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus($ids, $status)
    {
        return $this->calculatorResource->updateStatus($ids, $status);
    }

    /**
     * @param CalculatorInterface $object
     * @return void
     */
    protected function prepareData(CalculatorInterface $object)
    {
        /** @var Calculator $object */
        $data = $object->getData();
        if (!$object->getId()) {
            $object->unsetData('id');
        }

        if (isset($data['rule'])) {
            $conditionsArray = $object->getRule()
                ->loadPost($data['rule'])
                ->getConditions()
                ->asArray();
        } else {
            $conditionsArray = [];
        }
        $object->setConditionsSerialized(serialize($conditionsArray));
    }
}
