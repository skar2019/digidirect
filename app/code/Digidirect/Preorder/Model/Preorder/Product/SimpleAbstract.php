<?php

namespace Digidirect\PreOrder\Model\Preorder\Product;

use Digidirect\PreOrder\Helper\Data as PreOrderHelper;

/**
 * Class SimpleAbstract
 *
 * @package Digidirect\PreOrder\Model\Preorder\Product
 */
abstract class SimpleAbstract implements \Digidirect\PreOrder\Api\PreorderInterface
{
    /**
     * @var \Digidirect\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * Simple constructor.
     *
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     */
    public function __construct(
        PreOrderHelper $preOrderHelper
    ) {
        $this->preOrderHelper = $preOrderHelper;
    }
}
