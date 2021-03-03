<?php
namespace Digidirect\Navigation\Api\Data;

/**
 * Set interface.
 */
interface SetInterface
{
    const SET_ID = 'set_id';
    const SET_CODE = 'set_code';
    const NAME = 'name';
    const STATUS = 'status';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getSetId();

    /**
     * Get code
     *
     * @return string
     */
    public function getSetCode();

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set ID
     *
     * @param int $id
     * @return SetInterface
     */
    public function setSetId($id);

    /**
     * Set code
     *
     * @param string $code
     * @return SetInterface
     */
    public function setSetCode($code);

    /**
     * Set name
     *
     * @param string $name
     * @return SetInterface
     */
    public function setName($name);

    /**
     * Set status
     *
     * @param string $status
     * @return SetInterface
     */
    public function setStatus($status);
}
