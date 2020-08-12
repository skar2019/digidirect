<?php

namespace Ewave\AbstractGiftCard\Service\Data;

use Ewave\AbstractGiftCard\Model\ServiceInterface;

/**
 * Interface ServiceDataObjectInterface
 * @package Ewave\AbstractGiftCard\Service\Data
 * @api
 */
interface ServiceDataObjectInterface
{
    /**
     * Returns order
     *
     * @return OrderAdapterInterface
     */
    public function getOrder();

    /**
     * Returns service
     *
     * @return ServiceInterface
     */
    public function getService();
}
