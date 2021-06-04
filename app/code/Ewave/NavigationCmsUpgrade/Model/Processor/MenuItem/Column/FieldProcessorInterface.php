<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

/**
 * Interface FieldProcessorInterface
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
interface FieldProcessorInterface
{
    /**
     * @param array $data
     * @param string $key
     * @return mixed
     */
    public function getData(array $data, $key);
}
