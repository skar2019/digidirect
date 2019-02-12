<?php

namespace Ewave\ProductCalculator\Api\Data;

/**
 * Interface FieldInterface
 * @package Ewave\ProductCalculator\Api\Data
 */
interface FieldInterface
{
    const IMAGE_INFO_KEY = 'image_info';
    const ID = 'id';
    const NAME = 'name';
    const STATUS = 'status';
    const PRIORITY = 'priority';
    const LABEL = 'label';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const CATEGORIES_TO_SHOW    = 'categories_to_show';
    const CSS_CLASS = 'css_class';
    const IMAGE = 'image';

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     * @return \Ewave\ProductCalculator\Api\Data\FieldInterface
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
     * @return \Ewave\ProductCalculator\Api\Data\FieldInterface
     */
    public function setName($name);

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
     * @return \Ewave\ProductCalculator\Api\Data\FieldInterface
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
     * @return \Ewave\ProductCalculator\Api\Data\FieldInterface
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
     * @return \Ewave\ProductCalculator\Api\Data\FieldInterface
     */
    public function setLabel($label);

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
     * @return \Ewave\ProductCalculator\Api\Data\FieldInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

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
     * Set image
     *
     * @param string $image
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setImage($image);

    /**
     * Get image
     *
     * @return string
     */
    public function getImage();

    /**
     * @return array
     */
    public function getCategoriesToShow();

    /**
     * @param array $ids
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupInterface
     */
    public function setCategoriesToShow($ids);
}
