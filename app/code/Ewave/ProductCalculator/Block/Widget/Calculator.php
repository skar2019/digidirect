<?php

namespace Ewave\ProductCalculator\Block\Widget;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Ewave\ProductCalculator\Api\CalculatorRepositoryInterface;
use Ewave\ProductCalculator\Api\Data\CalculatorInterface;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Model\Media\Config as MediaConfig;

/**
 * Class Calculator
 *
 * @package Ewave\ProductCalculator\Calculator\Widget
 */
class Calculator extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    const CALCULATE_ROUTE = 'ewave_productcalculator/calculator/calculate';
    const ADDITIONAL_FIELDS_BLOCK = 'additional_calculator_fields';

    /**
     * @var CalculatorRepositoryInterface
     */
    protected $calculatorRepository;

    /**
     * @var MediaConfig
     */
    protected $mediaConfig;

    /**
     * @var CalculatorInterface
     */
    protected $calculator = null;

    /**
     * Calculator constructor.
     *
     * @param CalculatorRepositoryInterface $calculatorRepository
     * @param Context $context
     * @param MediaConfig $mediaConfig
     * @param array $data
     */
    public function __construct(
        CalculatorRepositoryInterface $calculatorRepository,
        Context $context,
        MediaConfig $mediaConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->calculatorRepository = $calculatorRepository;
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * @return CalculatorInterface|null
     */
    public function getCalculator()
    {
        if ($this->calculator === null) {
            try {
                $this->calculator = $this->calculatorRepository->getById($this->getData('calculator_id'));
            } catch (NoSuchEntityException $e) {
                $this->calculator = false;
            }
        }

        return $this->calculator ?: null;
    }

    /**
     * @return string
     */
    public function getCalculateUrl()
    {
        if ($calculator = $this->getCalculator()) {
            return $calculator->getFlagCustomLoadUrl()
                ? $calculator->getResultLoadUrl()
                : $this->getUrl(self::CALCULATE_ROUTE);
        }

        return '';
    }

    /**
     * Get additional fields html
     *
     * @return string
     */
    public function getAdditionalFieldsHtml()
    {
        $additionalFieldsHtml = '';
        if ($additionalFieldsBlock = $this->getChildBlock(self::ADDITIONAL_FIELDS_BLOCK)) {
            $additionalFieldsHtml = $additionalFieldsBlock->toHtml();
        }
        return $additionalFieldsHtml;
    }

    /**
     * Get Image Url
     *
     * @param FieldInterface $field
     * @return bool|string
     */
    public function getImageUrl($field)
    {
        return $field->getImage()
            ? $this->mediaConfig->getBaseMediaUrl() . $field->getImage()
            : false;
    }
}
