<?php

/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Paymentfee
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Paymentfee\Model\Calculation;

use Magento\Framework\Exception\ConfigurationMismatchException;
use Lof\Paymentfee\Helper\Data as FeeHelper;
use Lof\Paymentfee\Model\Config\Source\PriceType;
use Lof\Paymentfee\Model\Calculation\Calculator\FixedCalculator;
use Lof\Paymentfee\Model\Calculation\Calculator\PercentageCalculator;
use Lof\Paymentfee\Model\Calculation\Calculator\PerRowCalculator;
use Lof\Paymentfee\Model\Calculation\Calculator\PerItemCalculator;

class CalculatorFactory
{
    /**
     * @var FeeHelper
     */
    protected $helper;

    /**
     * @var FixedCalculator
     */
    private $fixedCalculator;

    /**
     * @var PercentageCalculator
     */
    private $percentageCalculator;

    /**
     * @var PerRowCalculator
     */
    private $perRowCalculator;

    /**
     * @var PerItemCalculator
     */
    private $perItemCalculator;

    /**
     * CalculatorFactory constructor.
     *
     * @param FeeHelper $helper
     */
    public function __construct(
        FeeHelper $helper,
        FixedCalculator $fixedCalculator,
        PercentageCalculator $percentageCalculator,
        PerRowCalculator $perRowCalculator,
        PerItemCalculator $perItemCalculator
    ) {
        $this->helper = $helper;
        $this->fixedCalculator = $fixedCalculator;
        $this->percentageCalculator = $percentageCalculator;
        $this->perRowCalculator = $perRowCalculator;
        $this->perItemCalculator = $perItemCalculator;
    }

    /**
     * @return Calculator\CalculatorInterface
     * @throws ConfigurationMismatchException
     */
    public function get()
    {
        switch ($this->helper->getPriceType()) {
            case PriceType::TYPE_FIXED:
                return $this->fixedCalculator;
            case PriceType::TYPE_PERCENTAGE:
                return $this->percentageCalculator;
            case PriceType::TYPE_PER_ROW:
                return $this->perRowCalculator;
            case PriceType::TYPE_PER_ITEM:
                return $this->perItemCalculator;
            default:
                throw new ConfigurationMismatchException(
                    __('Could not find price calculator for type %1', $this->helper->getPriceType())
                );
        }
    }
}
