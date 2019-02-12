<?php

namespace Ewave\ProductCalculator\Api;

use Ewave\ProductCalculator\Api\Data\CalculatorInterface;

interface CalculatorRepositoryInterface
{
    /**
     * Save Calculator
     * @param CalculatorInterface $calculator
     * @return CalculatorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CalculatorInterface $calculator);

    /**
     * Retrieve Calculator
     * @param string $calculatorId
     * @return CalculatorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($calculatorId);

    /**
     * Delete Calculator
     * @param CalculatorInterface $calculator
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CalculatorInterface $calculator);

    /**
     * Delete Calculator by ID
     * @param string $calculatorId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($calculatorId);

    /**
     * Update items status
     *
     * @param [] $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status);
}
