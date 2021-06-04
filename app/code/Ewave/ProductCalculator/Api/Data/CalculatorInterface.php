<?php

namespace Ewave\ProductCalculator\Api\Data;

/**
 * Interface CalculatorInterface
 * @package Ewave\ProductCalculator\Api\Data
 */
interface CalculatorInterface
{
    const ID                          = 'id';
    const NAME                        = 'name';
    const BUTTON_LABEL                = 'button_label';
    const PRODUCTS_COUNT              = 'products_count';
    const PRODUCTS_COUNT_FOR_CATEGORY = 'products_count_for_category';
    const SEPARATE_PRODUCTS_BY_CATEGORIES = 'separate_products_by_categories';
    const STATUS                      = 'status';
    const PRIORITY                    = 'priority';
    const CONDITIONS_SERIALIZED       = 'conditions_serialized';
    const FLAG_CUSTOM_LOAD_URL        = 'flag_custom_load_url';
    const RESULT_LOAD_ACTION          = 'result_load_action';
    const RESULT_LOAD_URL             = 'result_load_url';

    /**
     * Get id
     *
     * @return string
     */
    public function getId();

    /**
     * Set id
     *
     * @param string $id
     * @return CalculatorInterface
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return CalculatorInterface
     */
    public function setName($name);

    /**
     * Get button label
     *
     * @return string
     */
    public function getButtonLabel();

    /**
     * Set button label
     *
     * @param string $buttonLabel
     * @return CalculatorInterface
     */
    public function setButtonLabel($buttonLabel);

    /**
     * Get products count
     *
     * @return int
     */
    public function getProductsCount();

    /**
     * Set products count
     *
     * @param int $productsCount
     * @return CalculatorInterface
     */
    public function setProductsCount($productsCount);

    /**
     * Get products count for category
     *
     * @return int
     */
    public function getProductsCountForCategory();

    /**
     * Set products count for category
     *
     * @param int $productsCount
     * @return CalculatorInterface
     */
    public function setProductsCountForCategory($productsCount);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return CalculatorInterface
     */
    public function setStatus($status);

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive();

    /**
     * Get PRIORITY
     *
     * @return string
     */
    public function getPriority();

    /**
     * Set PRIORITY
     *
     * @param string $priority
     * @return CalculatorInterface
     */
    public function setPriority($priority);

    /**
     * Get conditions serialized
     *
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * Set conditions serialized
     *
     * @param string $conditionsSerialized
     * @return CalculatorInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * Get Result Load Action
     *
     * @return int
     */
    public function getResultLoadAction();

    /**
     * Is Result Load Action Redirect
     *
     * @return bool
     */
    public function isResultLoadActionRedirect();

    /**
     * Set Result Load Action
     *
     * @param int $resultLoadAction
     * @return CalculatorInterface
     */
    public function setResultLoadAction($resultLoadAction);

    /**
     * Get Result Load Url
     *
     * @return string
     */
    public function getResultLoadUrl();

    /**
     * Set Result Load Url
     *
     * @param string $resultLoadUrl
     * @return CalculatorInterface
     */
    public function setResultLoadUrl($resultLoadUrl);

    /**
     * Get use custom load url
     *
     * @return int
     */
    public function getFlagCustomLoadUrl();

    /**
     * Set use custom load url
     *
     * @param int $flagCustomLoadUrl
     * @return CalculatorInterface
     */
    public function setFlagCustomLoadUrl($flagCustomLoadUrl);

    /**
     * Get array of related field group category items
     *
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface[]|null
     */
    public function getFieldGroupCategories();

    /**
     * @return int
     */
    public function getSeparateProductsByCategories();

    /**
     * @param int $value
     * @return CalculatorInterface
     */
    public function setSeparateProductsByCategories($value);
}
