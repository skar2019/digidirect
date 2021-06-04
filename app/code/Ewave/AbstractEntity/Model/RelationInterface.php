<?php
namespace Ewave\AbstractEntity\Model;

/**
 * Interface RelationInterface
 * @package Ewave\AbstractEntity\Model
 */
interface RelationInterface
{
    /**
     * @param int $entityId
     * @param array $data
     * @return mixed
     */
    public function processRelation($entityId, $data);
}
