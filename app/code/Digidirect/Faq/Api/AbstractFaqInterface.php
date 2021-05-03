<?php
namespace Digidirect\Faq\Api;

/**
 * Interface AbstractFaqInterface
 * @package Digidirect\Faq\Api
 */
interface AbstractFaqInterface
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

    /**
     * Update status
     *
     * @param array $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status);
}
