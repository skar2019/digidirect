<?php

namespace Ewave\PreOrder\Model\Preorder\Product;

use Ewave\PreOrder\Helper\Data as PreOrderHelper;

/**
 * Class SimpleAbstract
 *
 * @package Ewave\PreOrder\Model\Preorder\Product
 */
abstract class SimpleAbstract implements \Ewave\PreOrder\Api\PreorderInterface
{
    /**
     * @var \Ewave\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * Simple constructor.
     *
     * @param \Ewave\PreOrder\Helper\Data $preOrderHelper
     */
    public function __construct(
        PreOrderHelper $preOrderHelper
    ) {
        $this->preOrderHelper = $preOrderHelper;
    }
}
