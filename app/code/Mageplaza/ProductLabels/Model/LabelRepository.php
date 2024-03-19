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

namespace Mageplaza\ProductLabels\Model;

use Exception;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\ProductLabels\Api\Data\LabelSearchResultInterface;
use Mageplaza\ProductLabels\Api\Data\LabelSearchResultInterfaceFactory;
use Mageplaza\ProductLabels\Api\LabelRepositoryInterface;
use Mageplaza\ProductLabels\Helper\Data as Helper;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule\Collection;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule\CollectionFactory;

/**
 * Class LabelRepository
 * @package Mageplaza\ProductLabels\Model
 */
class LabelRepository implements LabelRepositoryInterface
{
    /**
     * @var Helper
     */
    protected $helperData;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var LabelSearchResultInterfaceFactory
     */
    protected $labelSearchResultInterfaceFactory;

    /**
     * LabelRepository constructor.
     *
     * @param Helper $helperData
     * @param RuleFactory $ruleFactory
     * @param CollectionFactory $ruleCollectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @param LabelSearchResultInterfaceFactory $labelSearchResultInterfaceFactory
     */
    public function __construct(
        Helper $helperData,
        RuleFactory $ruleFactory,
        CollectionFactory $ruleCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor,
        LabelSearchResultInterfaceFactory $labelSearchResultInterfaceFactory
    ) {
        $this->helperData                        = $helperData;
        $this->ruleFactory                       = $ruleFactory;
        $this->ruleCollectionFactory             = $ruleCollectionFactory;
        $this->searchCriteriaBuilder             = $searchCriteriaBuilder;
        $this->collectionProcessor               = $collectionProcessor;
        $this->labelSearchResultInterfaceFactory = $labelSearchResultInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById($ruleId)
    {
        if (!$this->helperData->isEnabled()) {
            return null;
        }

        $rule = $this->ruleFactory->create()->load($ruleId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(
                __("The rule that was requested doesn't exist. Please try again.")
            );
        }
        $this->helperData->processImageUrl($rule);

        return $rule;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($ruleId)
    {
        if (!$this->helperData->isEnabled()) {
            return null;
        }

        $rule = $this->ruleFactory->create()->load($ruleId);

        if (!$rule->getId()) {
            throw new NoSuchEntityException(
                __("The rule that was requested doesn't exist. Please try again.")
            );
        }

        try {
            $rule->delete();
        } catch (Exception $e) {
            throw new NoSuchEntityException(__('The rule can\'t be delete.'));
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function add($label)
    {
        if (!$this->helperData->isEnabled()) {
            return null;
        }

        $ruleModel = $this->ruleFactory->create();

        if ($label->getId()) {
            $ruleModel->load($label->getId());

            if (!$ruleModel->getId()) {
                throw new NoSuchEntityException(__('The label doesn\'t exist.'));
            }
        }

        $ruleModel->addData(Helper::jsonDecode(Helper::jsonEncode($label)));

        try {
            $ruleModel->save();
        } catch (Exception $e) {
            throw new NoSuchEntityException(__('The rule label can\'t be saved.'));
        }

        return $ruleModel;
    }

    /**
     * @inheritDoc
     */
    public function update($label)
    {
        if (!$this->helperData->isEnabled()) {
            return null;
        }

        if (!$label->getId()) {
            throw new NoSuchEntityException(__('Missing id field'));
        }

        $ruleModel = $this->ruleFactory->create()->load($label->getId());

        if (!$ruleModel->getId()) {
            throw new NoSuchEntityException(__('The label doesn\'t exist.'));
        }

        $ruleModel->addData(Helper::jsonDecode(Helper::jsonEncode($label)));

        try {
            $ruleModel->save();
        } catch (Exception $e) {
            throw new NoSuchEntityException(__('The rule label can\'t be saved.'));
        }

        return $ruleModel;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        if (!$this->helperData->isEnabled()) {
            return null;
        }

        /** @var Collection $ruleCollection */
        $ruleCollection = $this->ruleCollectionFactory->create();
        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $ruleCollection);
        }
        /** @var LabelSearchResultInterface $searchResult */
        $searchResult = $this->labelSearchResultInterfaceFactory->create();
        foreach ($ruleCollection->getItems() as $item) {
            $this->helperData->processImageUrl($item);
        }
        $searchResult->setItems($ruleCollection->getItems());
        $searchResult->setTotalCount($ruleCollection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}
