<?php

namespace Ewave\AbstractGiftCard\Service\Data;

use Ewave\AbstractGiftCard\Model\ServiceInterface;

interface ServiceDataObjectFactoryInterface
{
    /**
     * Creates Service Data Object
     *
     * @param ServiceInterface $serviceInfo
     * @return ServiceDataObjectInterface
     */
    public function create(ServiceInterface $serviceInfo);
}
