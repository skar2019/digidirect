<?php
namespace Ewave\Navigation\Api;

/**
 * Interface AbstractNavigationInterface
 * @package Ewave\Navigation\Api
 */
interface AbstractNavigationInterface
{
    /**
     * Delete record by ID
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id);

    /**
     * Get object by ID
     *
     * @param int $id
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getById($id);
}
