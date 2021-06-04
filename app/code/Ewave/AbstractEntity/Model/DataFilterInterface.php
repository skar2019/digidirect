<?php

namespace Ewave\AbstractEntity\Model;

/**
 * Interface DataFilterInterface
 * @package Ewave\AbstractEntity\Model
 */
interface DataFilterInterface
{
    /**
     * @param array $data
     * @return void
     */
    public function execute(array &$data);
}
