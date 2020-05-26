<?php

namespace Ewave\Vii\Service\Request\PreAuth;

use Ewave\Vii\Service\Request\AbstractBuilder;
use Ewave\AbstractGiftCard\Service\Helper\SubjectReader;
use Ewave\AbstractGiftCard\Service\Request\BuilderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class RequestBuilder
 * @package Ewave\Vii\Service\Request\PreAuth
 */
class RequestBuilder extends AbstractBuilder implements BuilderInterface
{
    const REQUEST_TYPE = 'PreAuthRequest';

    /**
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(array $buildSubject)
    {
        /** @var \Ewave\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO */
        $serviceDO = SubjectReader::readService($buildSubject);
        $store = $serviceDO->getService()->getStore();
        $this->initRequestParams($store);
        $requestDataObject = $this->createRequestDataObject(['amount' => SubjectReader::readAmount($buildSubject)]);
        return ['xml' => $this->getRequestBody($serviceDO, $requestDataObject)];
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
        $quote = $serviceDO->getQuote();
        $requestParams = parent::getRequestParams();
        $requestParams[self::REQUEST_CARD_NUMBER_FIELD] = $entity->getData('code');
        $requestParams[self::REQUEST_PIN_FIELD] = $entity->getData('pin');
        $requestParams[self::REQUEST_AMOUNT_FIELD] = number_format($requestDataObject->getData('amount'), 2, '.', '');
        $requestParams[self::REQUEST_EXTERNAL_REFERENCE] = $quote->getId();
        return $this->assocToXml($requestParams, self::SECTION_ROOT);
    }
}
