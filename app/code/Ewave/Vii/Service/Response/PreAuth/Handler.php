<?php

namespace Ewave\Vii\Service\Response\PreAuth;

use Ewave\AbstractGiftCard\Service\Helper\SubjectReader;
use Ewave\AbstractGiftCard\Service\Response\HandlerInterface;
use Ewave\Vii\Service\Response\CheckStatus\Handler as CheckStatusHandle;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\DataObject;
use Ewave\AbstractGiftCard\Model\AbstractGiftCardEntity;

/**
 * Class Handler
 * @package Ewave\Vii\Service\Response\PreAuth
 */
class Handler implements HandlerInterface
{
    const PRE_AUTH_CODE = 'PreAuthCode';
    const AMOUNT = 'Amount';

    /**
     * @var \Ewave\AbstractGiftCard\Service\Helper\GiftCardGenerator
     */
    protected $_cardGenerator;

    /**
     * @var \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface
     */
    protected $abstractGiftCardEntityRepository;

    /**
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $giftCardAccountFactory;

    /**
     * @var \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface
     */
    protected $baseGiftCardEntityRepository;

    /**
     * Handler constructor.
     * @param \Ewave\AbstractGiftCard\Service\Helper\GiftCardGenerator $cardGenerator
     * @param \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $baseGiftCardEntityRepository
     */
    public function __construct(
        \Ewave\AbstractGiftCard\Service\Helper\GiftCardGenerator $cardGenerator,
        \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $baseGiftCardEntityRepository
    ) {
        $this->_cardGenerator = $cardGenerator;
        $this->abstractGiftCardEntityRepository = $abstractGiftCardEntityRepository;
        $this->giftCardAccountFactory = $giftcardaccountFactory;
        $this->baseGiftCardEntityRepository = $baseGiftCardEntityRepository;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(array $handlingSubject, array $response)
    {
        $serviceDO = SubjectReader::readService($handlingSubject);
        $checkResponse = $serviceDO->getCheckResponse();
        $dateExpires = \DateTime::createFromFormat(
            CheckStatusHandle::EXPIRATION_DATE_FORMAT,
            $checkResponse[CheckStatusHandle::EXPIRY_DATE]
        )->format(DateTime::DATETIME_PHP_FORMAT);

        $giftCardData = new DataObject([
            'code' => $checkResponse[CheckStatusHandle::CODE],
            'pin'  => $serviceDO->getService()->getAbstractGiftCardEntity()->getData(CheckStatusHandle::PIN),
            'balance' => $checkResponse[self::AMOUNT],
            'date_expires' => $dateExpires,
            'card_status' => $checkResponse[CheckStatusHandle::CARD_STATUS_ID]
        ]);

        $giftCardAccount = $this->_cardGenerator->initGiftCardAccount($serviceDO->getService(), $giftCardData);
        $entity = $this->baseGiftCardEntityRepository->loadByGiftCardAccount($giftCardAccount);

        $entityQuoteData = new \Magento\Framework\DataObject(['status' => AbstractGiftCardEntity::STATUS_HOLD]);
        $entityQuoteData->setQuoteId($serviceDO->getQuote()->getId());
        $entityQuoteData->setAmount($checkResponse[self::AMOUNT]);
        $entityQuoteData->setToken($checkResponse[self::PRE_AUTH_CODE]);
        $entityQuoteData->setAbstractGiftCardEntityId($entity->getEntityId());
        $this->abstractGiftCardEntityRepository->saveEntityQuoteData($entityQuoteData);
        $serviceDO->getService()->setLastToken($checkResponse[self::PRE_AUTH_CODE]);
        return $this;
    }
}
