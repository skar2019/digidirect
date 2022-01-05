<?php

namespace Digidirect\Vii\Service\Request\Redemption;

use Digidirect\Vii\Service\Request\AbstractBuilder;
use Digidirect\AbstractGiftCard\Service\Helper\SubjectReader;
use Digidirect\AbstractGiftCard\Service\Request\BuilderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class RequestBuilder
 * @package Digidirect\Vii\Service\Request\Redemption
 */
class RequestBuilder extends AbstractBuilder implements BuilderInterface
{
    const REQUEST_TYPE = 'Redemption';
    const REQUEST_AUTH_CODE = 'PreAuthCode';

    /**
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    protected  $logger;

    public function __construct(
        \Digidirect\CustomOrderLog\Logger\Logger $logger
    ) {
        $this->logger = $logger;
    }

    public function build(array $buildSubject)
    {
        /** @var \Digidirect\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO */
        $this->logger->info('Redemption Build');
        $serviceDO = SubjectReader::readService($buildSubject);
        $store = $serviceDO->getService()->getStore();
        $this->initRequestParams($store);
        $data = ['amount' => SubjectReader::readAmount($buildSubject)];

        try {
            $token = SubjectReader::readToken($buildSubject);
            $data['token'] = $token;
            //@codingStandardsIgnoreStart
        } catch (\InvalidArgumentException $e) {

        }
        //@codingStandardsIgnoreEnd

        $requestDataObject = $this->createRequestDataObject($data);
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
        $service = $serviceDO->getService();
        $entity = $service->getAbstractGiftCardEntity();
        $order = $service->getOrder();
        $requestParams = parent::getRequestParams();
        $service->setLastTransId($requestParams[self::REQUEST_TRAN_ID_FIELD]);
        $requestParams[self::REQUEST_CARD_NUMBER_FIELD] = $entity->getData('code');
        $requestParams[self::REQUEST_PIN_FIELD] = $entity->getData('pin');
        $requestParams[self::REQUEST_AMOUNT_FIELD] = number_format($requestDataObject->getData('amount'), 2, '.', '');
        $requestParams[self::REQUEST_EXTERNAL_REFERENCE] = $order->getQuoteId();
        if ($requestDataObject->getData('token')) {
            $this->logger->info('Redemption Token: '.$requestDataObject->getData('token'));
            $requestParams[self::REQUEST_AUTH_CODE] = $requestDataObject->getData('token');
        }
        return $this->assocToXml($requestParams, self::SECTION_ROOT);
    }
}
