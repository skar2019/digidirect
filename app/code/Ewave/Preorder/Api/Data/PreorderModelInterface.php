<?php

namespace Ewave\PreOrder\Api\Data;

/**
 * @api
 */
interface PreorderModelInterface
{
    /**#@-
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const TYPE = 'type';
    const MODEL = 'model';
    /**#@-*/

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get model
     *
     * @return string|null
     */
    public function getModel();

    /**
     * Set model
     *
     * @param string $model
     * @return $this
     */
    public function setModel($model);
}
