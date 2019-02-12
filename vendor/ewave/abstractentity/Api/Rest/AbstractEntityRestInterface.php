<?php
namespace Ewave\AbstractEntity\Api\Rest;

/**
 * Interface AbstractEntityRestInterface
 * @package Ewave\AbstractEntity\Api\Rest
 */
interface AbstractEntityRestInterface
{
    /**
     * Retrieve AbstractEntity
     * @param string $id
     * @param string|null $attributes
     * @return string
     */
    public function getById($id, $attributes = null);
}
