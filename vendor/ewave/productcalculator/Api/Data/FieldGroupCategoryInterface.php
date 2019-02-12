<?php
namespace Ewave\ProductCalculator\Api\Data;

/**
 * Interface FieldGroupCategoryInterface
 * @package Ewave\ProductCalculator\Api\Data
 */
interface FieldGroupCategoryInterface
{
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const STATUS = 'status';
    const PRIORITY = 'priority';
    const LABEL = 'label';

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
     * Get array of related field items
     *
     * @return \Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface[]|null
     */
    public function getFieldGroups();
}
