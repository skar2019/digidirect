<?php
namespace Ewave\Blog\Model\Config;

/**
 * Interface SourceProviderInterface
 */
interface SourceProviderInterface
{
    /**
     * @return array
     */
    public function getOptions();
}
