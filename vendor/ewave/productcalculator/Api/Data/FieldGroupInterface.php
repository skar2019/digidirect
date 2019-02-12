<?php

namespace Ewave\ProductCalculator\Api\Data;

/**
 * Interface FieldGroupInterface
 * @package Ewave\ProductCalculator\Api\Data
 */
interface FieldGroupInterface
{
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const STATUS = 'status';
    const PRIORITY = 'priority';
    const LABEL = 'label';
    const IS_MANDATORY = 'is_mandatory';
    const MANDATORY_USER_NOTICE = 'mandatory_user_notice';
    const TYPE = 'type';
    const CSS_CLASS = 'css_class';
    const CATEGORY_ID = 'category_id';
    const CUSTOM_INPUT_NAME = 'custom_input_name';
    const VALUE_FIELD = 'value_field';

    /**
     * Set id
     *
     * @param int $id
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setId($id);

    /**
     * Set name
     *
     * @param string $name
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set description
     *
     * @param string $description
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setStatus($status);

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive();

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority();

    /**
     * Set priority
     *
     * @param int $priority
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setPriority($priority);

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param string $label
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setLabel($label);

    /**
     * Get is mandatory
     *
     * @return bool
     */
    public function isMandatory();

    /**
     * Set is mandatory
     *
     * @param bool $isMandatory
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setIsMandatory($isMandatory);

    /**
     * Get mandatory user notice
     *
     * @return string
     */
    public function getMandatoryUserNotice();

    /**
     * Set mandatory user notice
     *
     * @param string $mandatoryUserNotice
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setMandatoryUserNotice($mandatoryUserNotice);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setType($type);

    /**
     * Set css class
     *
     * @param string $cssClass
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setCssClass($cssClass);

    /**
     * Get css class
     *
     * @return string
     */
    public function getCssClass();

    /**
     * Set category Id
     *
     * @param int $categoryId
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setCategoryId($categoryId);

    /**
     * Get category Id
     *
     * @return string
     */
    public function getCategoryId();

    /**
     * Set custom input name
     *
     * @param int $customInputName
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setCustomInputName($customInputName);

    /**
     * Get custom input name
     *
     * @return string
     */
    public function getCustomInputName();

    /**
     * Set value field
     *
     * @param int $valueField
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setValueField($valueField);

    /**
     * Get value field
     *
     * @return string
     */
    public function getValueField();

    /**
     * Get array of related field items
     *
     * @return \Ewave\ProductCalculator\Api\Data\FieldInterface[]|null
     */
    public function getRelatedFields();
}
