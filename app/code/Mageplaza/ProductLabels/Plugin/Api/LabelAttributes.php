<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Plugin\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\ProductLabels\Block\Label;
use Mageplaza\ProductLabels\Helper\Data;
use Mageplaza\ProductLabels\Model\LabelRepository;
use Mageplaza\ProductLabels\Model\Rule;

/**
 * Class LabelAttributes
 * @package Mageplaza\ProductLabels\Plugin\Api
 */
class LabelAttributes
{
    /**
     * @var LabelRepository
     */
    protected $labelRepository;

    /**
     * @var Label
     */
    protected $label;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * LabelAttributes constructor.
     *
     * @param Label $label
     * @param Data $helperData
     * @param LabelRepository $labelRepository
     */
    public function __construct(
        Label $label,
        Data $helperData,
        LabelRepository $labelRepository
    ) {
        $this->label           = $label;
        $this->helperData      = $helperData;
        $this->labelRepository = $labelRepository;
    }

    /**
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $entity
     *
     * @return ProductInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(Unused)
     */
    public function afterGet(
        ProductRepositoryInterface $subject,
        ProductInterface $entity
    ) {
        if (!$this->helperData->isEnabled()) {
            return $entity;
        }

        $labelData = [];

        /** @var Rule $rule */
        foreach ($this->label->getRulesApplyProduct($entity) as $rule) {
            if ($this->label->validateProductInRule($rule, $entity->getId())) {
                $labelData[] = $this->labelRepository->getById($rule->getId());
            }
        }

        $extensionAttributes = $entity->getExtensionAttributes();

        if ($extensionAttributes !== null) {
            $extensionAttributes->setMpLabelData($labelData);
        }
        $entity->setExtensionAttributes($extensionAttributes);

        return $entity;
    }

    /**
     * @param ProductRepositoryInterface $subject
     * @param SearchResults $searchCriteria
     *
     * @return SearchResults
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(Unused)
     */
    public function afterGetList(
        ProductRepositoryInterface $subject,
        SearchResults $searchCriteria
    ) {
        $products = [];
        /** @var ProductInterface $entity */
        foreach ($searchCriteria->getItems() as $entity) {
            $labelData = [];
            /** @var Rule $rule */
            foreach ($this->label->getRulesApplyProduct($entity) as $rule) {
                if ($this->label->validateProductInRule($rule, $entity->getId())) {
                    $labelData[] = $this->labelRepository->getById($rule->getId());
                }
            }

            $extensionAttributes = $entity->getExtensionAttributes();
            if ($extensionAttributes !== null) {
                $extensionAttributes->setMpLabelData($labelData);
            }

            $entity->setExtensionAttributes($extensionAttributes);

            $products[] = $entity;
        }
        $searchCriteria->setItems($products);

        return $searchCriteria;
    }
}
