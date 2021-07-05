<?php

namespace Digidirect\Vii\Service\Request\PreAuthCancellation;

use Digidirect\Vii\Service\Request\AbstractBuilder;
use Digidirect\AbstractGiftCard\Service\Helper\SubjectReader;
use Digidirect\AbstractGiftCard\Service\Data\ServiceDataObjectInterface;
use Digidirect\AbstractGiftCard\Service\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class RequestBuilder
 * @package Digidirect\Vii\Service\Request\PreAuthCancellation
 */
class RequestBuilder extends AbstractBuilder implements BuilderInterface
{
    const REQUEST_TYPE = 'PreAuthCancellation';
    const REQUEST_AUTH_CODE = 'PreAuthCode';

    /**
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(array $buildSubject)
    {
        /** @var \Digidirect\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO */
        $serviceDO = SubjectReader::readService($buildSubject);
        $store = $serviceDO->getService()->getStore();
        $this->initRequestParams($store);
        $requestDataObject = $this->createRequestDataObject(['token' => SubjectReader::readToken($buildSubject)]);
        return ['xml' => $this->getRequestBody($serviceDO, $requestDataObject)];
    }

    /**
     * @param \Digidirect\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO
     * @param \Magento\Framework\DataObject $requestDataObject = null
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     * @return string
     */
    public function getRequestBody(
        \Digidirect\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO,
        \Magento\Framework\DataObject $requestDataObject = null
    ) {
        $entity = $serviceDO->getService()->getAbstractGiftCardEntity();
        $service = $serviceDO->getService();
        $object = $service->getOrder() ?? ($service->getQuote() ?? $serviceDO->getQuote());
        $quoteId = ($object instanceof OrderInterface) ? $object->getQuoteId() : $object->getId();
        $requestParams = parent::getRequestParams();
        $requestParams[self::REQUEST_CARD_NUMBER_FIELD] = $entity->getData('code');
        $requestParams[self::REQUEST_EXTERNAL_REFERENCE] = $quoteId;
        $requestParams[self::REQUEST_AUTH_CODE] = $requestDataObject->getData('token');
        return $this->assocToXml($requestParams, self::SECTION_ROOT);
    }
}
