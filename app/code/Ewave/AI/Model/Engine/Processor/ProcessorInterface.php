<?php
namespace Ewave\AI\Model\Engine\Processor;

/**
 * Interface ProcessorInterface
 *
 * @package Ewave\AI\Model\Engine\Processor
 */
interface ProcessorInterface
{
    /**
     * Process integration
     *
     * @return boolean
     */
    public function process();

    /**
     * @return mixed
     */
    public function getResult();
}
