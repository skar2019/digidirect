<?php

namespace Ewave\Locator\Model\Config;

class Reader extends \Ewave\Utilities\Model\Config\Reader
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = ['/config/entity' => 'name'];
}
