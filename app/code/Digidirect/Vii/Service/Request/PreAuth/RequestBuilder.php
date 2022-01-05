<?php

namespace Digidirect\Vii\Service\Request\PreAuth;

use Digidirect\Vii\Service\Request\AbstractBuilder;
use Digidirect\AbstractGiftCard\Service\Helper\SubjectReader;
use Digidirect\AbstractGiftCard\Service\Request\BuilderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class RequestBuilder
 * @package Digidirect\Vii\Service\Request\PreAuth
 */
class RequestBuilder extends AbstractBuilder implements BuilderInterface
{
    const REQUEST_TYPE = 'PreAuthRequest';

    protected $_logger;

    public function __construct(
        \Digidirect\CustomLogGC\Logger\Logger $logger
    ) {
        $this->_logger = $logger;
    }

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
        $requestDataObject = $this->createRequestDataObject(['amount' => SubjectReader::readAmount($buildSubject)]);
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
        $quote = $serviceDO->getQuote();
        $requestParams = parent::getRequestParams();
        $this->_logger->info('PreAuth '.$entity->getData('code'));
        $requestParams[self::REQUEST_CARD_NUMBER_FIELD] = $entity->getData('code');
        $requestParams[self::REQUEST_PIN_FIELD] = $entity->getData('pin');
        $requestParams[self::REQUEST_AMOUNT_FIELD] = number_format($requestDataObject->getData('amount'), 2, '.', '');
        $requestParams[self::REQUEST_EXTERNAL_REFERENCE] = $quote->getId();
        return $this->assocToXml($requestParams, self::SECTION_ROOT);
    }
}
