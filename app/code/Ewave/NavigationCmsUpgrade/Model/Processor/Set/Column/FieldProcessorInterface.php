<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\Set\Column;

/**
 * Interface FieldProcessorInterface
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor\Set
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
