<?php
namespace Ewave\Utilities\Model\System\Config\Frontend;

/**
 * Interface AjaxAutoCompleteInterface
 * @package Ewave\Utilities\Model\System\Config\Frontend
 */
interface AjaxAutoCompleteInterface
{
    /**
     * @return string
     */
    public function getAjaxUrl();

    /**
     * @return mixed
     */
    public function getCurrentId();

    /**
     * @param mixed $id
     * @return mixed
     */
    public function getNameById($id);
}
