<?php

namespace Digidirect\Vii\Observer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GiftCardAccount\Model\Giftcardaccount;
use Digidirect\AbstractGiftCard\Model\AbstractGiftCardEntity;

/**
 * Class QuoteSubmitSuccess
 * @package Digidirect\Vii\Observer
 */
class QuoteSubmitSuccess extends \Digidirect\AbstractGiftCard\Observer\QuoteSubmitSuccess
{
    /**
     * @var \Digidirect\Vii\Api\AbstractGiftCardEntityRepositoryInterface
     */
    protected $viiGiftCardEntityRepository;

    /**
     * @var \Digidirect\Vii\Service\Config\Config
     */
    protected $config;

    /**
     * QuoteSubmitSuccess constructor.
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param \Digidirect\AbstractGiftCard\Helper\Data $helper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     * @param \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
     * @param \Digidirect\Vii\Api\AbstractGiftCardEntityRepositoryInterface $viiGiftCardEntityRepository
     * @param \Digidirect\Vii\Service\Config\Config $config
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        \Digidirect\AbstractGiftCard\Helper\Data $helper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory,
        \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository,
        \Digidirect\Vii\Api\AbstractGiftCardEntityRepositoryInterface $viiGiftCardEntityRepository,
        \Digidirect\Vii\Service\Config\Config $config
    ) {
        parent::__construct($giftCAHelper, $helper, $giftcardaccountFactory, $abstractGiftCardEntityRepository);
        $this->viiGiftCardEntityRepository = $viiGiftCardEntityRepository;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order $order
         */
        if (!$this->config->isActive()) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        $cards = $this->giftCAHelper->getCards($order);
        if (empty($cards)) {
            return;
        }

        foreach ($cards as $giftCard) {
            try {
                $giftCardAccount = $this->giftCardAccountFactory->create()
                    ->loadByCode($giftCard[Giftcardaccount::CODE]);

                $entity = $this->abstractGiftCardEntityRepository->loadByGiftCardAccount($giftCardAccount);
                $entityQuoteData = $this->viiGiftCardEntityRepository->getEntityQuoteData(
                    $entity,
                    $order->getQuoteId()
                );
                if ($entityQuoteData && $entityQuoteData->getToken()) {
                    $entity->setToken($entityQuoteData->getToken());
                    $entity->setStatus(AbstractGiftCardEntity::STATUS_HOLD);
                }

                if ($giftCard[Giftcardaccount::CODE] != $entity->getCode()) {
                    continue;
                }

                $amount = $giftCard[Giftcardaccount::AUTHORIZED];
                $entityOrderData = new \Magento\Framework\DataObject(['status' => $entity->getStatus()]);
                
                /*Update 08/11/2020** 
                Needs to execute redemption as long as it is execute Pre Auth 
                if ($dbState == $state && !$isAcceptForPaid) { //state was not changed
                     return;
                }
                if ($this->isAcceptAvailable($order, $entity, $giftCard)) {
                    $service = $entity->getService();
                    $service->setStore($order->getStoreId());
                    $service->setOrder($order);
                    $service->validate()->accept($amount, $entity->getToken());
                    $entityOrderData->setStatus(AbstractGiftCardEntity::STATUS_ACCEPT);
                }
                */
                
                $service = $entity->getService();
                $service->setStore($order->getStoreId());
                $service->setOrder($order);
                $service->validate()->accept($amount, $entity->getToken());
                $entityOrderData->setStatus(AbstractGiftCardEntity::STATUS_ACCEPT);
                
                $entityOrderData->setOrderId($order->getId());
                $entityOrderData->setAmount($amount);
                $entityOrderData->setToken($entity->getToken());
                $entityOrderData->setAbstractGiftCardEntityId($entity->getEntityId());
                $this->abstractGiftCardEntityRepository->saveEntityOrderData($entityOrderData);
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }
    }
}
