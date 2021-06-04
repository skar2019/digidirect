<?php
namespace Ewave\CheckoutFields\Api\Data;

/**
 * Interface CronActionInterface
 * @package Ewave\CheckoutFields\Api\Data
 */
interface CronActionInterface
{
    /**
     * @return void
     */
    public function runAction();

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getName();
}
