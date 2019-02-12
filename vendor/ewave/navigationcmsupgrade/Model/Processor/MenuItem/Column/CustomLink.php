<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

/**
 * Class CustomLink
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class CustomLink implements FieldProcessorInterface
{
    /**
     * @param [] $data
     * @param string $key
     * @return mixed
     */
    public function getData(array $data, $key)
    {
        return $data[$key] ?? null;
    }
}
