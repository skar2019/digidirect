<?php

namespace Ewave\Vii\Service\Request\Undo;

use Ewave\Vii\Service\Request\AbstractBuilder;
use Ewave\Vii\Service\Helper\SubjectReader;
use Ewave\AbstractGiftCard\Service\Request\BuilderInterface;

/**
 * Class RequestBuilder
 * @package Ewave\Vii\Service\Request\Undo
 */
class RequestBuilder extends AbstractBuilder implements BuilderInterface
{
    const REQUEST_TYPE = 'Undo';
    const REQUEST_TRANS_ID_TO_UNDO = 'viiTranIdToUndo';

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
        $data = ['trans_id' => SubjectReader::readTransId($buildSubject)];
        $requestDataObject = $this->createRequestDataObject($data);
        return ['xml' => $this->getRequestBody($serviceDO, $requestDataObject)];
    }

    /**
     * @param \Ewave\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO
     * @param \Magento\Framework\DataObject $requestDataObject = null
     * @return string
     */
    public function getRequestBody(
        \Ewave\AbstractGiftCard\Service\Data\ServiceDataObject $serviceDO,
        \Magento\Framework\DataObject $requestDataObject = null
    ) {
        $service = $serviceDO->getService();
        $entity = $service->getAbstractGiftCardEntity();
        $requestParams = parent::getRequestParams();
        $requestParams[self::REQUEST_CARD_NUMBER_FIELD] = $entity->getData('code');
        $requestParams[self::REQUEST_TRANS_ID_TO_UNDO] = $requestDataObject->getData('trans_id');
        return $this->assocToXml($requestParams, self::SECTION_ROOT);
    }
}
