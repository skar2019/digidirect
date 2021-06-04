<?php
namespace Ewave\CmsUpgrade\Model;

/**
 * Interface GeneratorInterface
 * @package Ewave\CmsUpgrade\Model
 */
interface GeneratorInterface
{
    /**
     * @return mixed
     */
    public function generate();

    /**
     * @return mixed
     */
    public function getUpgradeFields();

    /**
     * @return mixed
     */
    public function getEntityType();
}
