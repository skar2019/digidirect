<?php
namespace Ewave\CheckoutFields\Model\Config;

use Magento\Framework\Config\Reader\Filesystem;

/**
 * Class Reader
 * @package Ewave\CheckoutFields\Model\Config
 */
class Reader extends \Ewave\Utilities\Model\Config\Reader
{
    /**
     * @var array
     */
    protected $_idAttributes = ['/config/field' => 'id'];
}
