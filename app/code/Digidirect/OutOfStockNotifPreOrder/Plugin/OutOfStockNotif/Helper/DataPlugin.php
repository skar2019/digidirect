<?php
namespace Digidirect\OutOfStockNotifPreOrder\Plugin\OutOfStockNotif\Helper;

use Digidirect\PreOrder\Model\Preorder\Product\Simple;

/**
 * Class DataPlugin
 * @package Digidirect\OutOfStockNotifPreOrder\Plugin\Digidirect\OutOfStockNotif\Helper
 */
class DataPlugin
{
    /**
     * @var Simple
     */
    protected $simple;

    /**
     * DataPlugin constructor.
     * @param Simple $simple
     */
    public function __construct(
        Simple $simple
    ) {
        $this->simple = $simple;
    }

    /**
     * Additional condition for displaying the "Notify me when in stock" button
     *
     * @param \Digidirect\OutOfStockNotif\Helper\Data $subject
     * @param bool $result
     * @return bool
     */
    public function afterShowButton($subject, $result)
    {
        if ($result) {
            $product = $subject->getCurrentProduct();
            $isProductPreorder = $this->simple->isProductPreorder($product);

            return !$isProductPreorder;
        }

        return $result;
    }
}
