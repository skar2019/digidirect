<?php

namespace Ewave\Vii\Service\Request\CheckStatus;

use Ewave\Vii\Service\Request\AbstractBuilder;
use Ewave\AbstractGiftCard\Service\Helper\SubjectReader;
use Ewave\AbstractGiftCard\Service\Data\ServiceDataObjectInterface;
use Ewave\AbstractGiftCard\Service\Request\BuilderInterface;

/**
 * Class RequestBuilder
 * @package Ewave\Vii\Service\Request\CheckStatus
 */
class RequestBuilder extends AbstractBuilder implements BuilderInterface
{
    /**
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(array $buildSubject)
    {
        /** @var ServiceDataObjectInterface $serviceDO */
        $serviceDO = SubjectReader::readService($buildSubject);
        $store = $serviceDO->getService()->getStore();
        $this->initRequestParams($store);
        return ['xml' => $this->getRequestBody($serviceDO)];
    }

    /**
     * @param \Ewave\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO
     * @param \Magento\Framework\DataObject $requestDataObject = null
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     * @return string
     */
    public function getRequestBody(
        \Ewave\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO,
        \Magento\Framework\DataObject $requestDataObject = null
    ) {
        $entity = $serviceDO->getService()->getAbstractGiftCardEntity();
        $requestParams = parent::getRequestParams();
        $requestParams[self::REQUEST_CARD_NUMBER_FIELD] = $entity->getData('code');
        $requestParams[self::REQUEST_PIN_FIELD] = $entity->getData('pin');
        return $this->assocToXml($requestParams, self::SECTION_ROOT);
    }
}
