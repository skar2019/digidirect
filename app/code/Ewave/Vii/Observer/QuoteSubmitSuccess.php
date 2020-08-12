<?php

namespace Ewave\Vii\Observer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GiftCardAccount\Model\Giftcardaccount;
use Ewave\AbstractGiftCard\Model\AbstractGiftCardEntity;

/**
 * Class QuoteSubmitSuccess
 * @package Ewave\Vii\Observer
 */
class QuoteSubmitSuccess extends \Ewave\AbstractGiftCard\Observer\QuoteSubmitSuccess
{
    /**
     * @var \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface
     */
    protected $viiGiftCardEntityRepository;

    /**
     * @var \Ewave\Vii\Service\Config\Config
     */
    protected $config;

    /**
     * QuoteSubmitSuccess constructor.
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param \Ewave\AbstractGiftCard\Helper\Data $helper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
     * @param \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface $viiGiftCardEntityRepository
     * @param \Ewave\Vii\Service\Config\Config $config
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        \Ewave\AbstractGiftCard\Helper\Data $helper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository,
        \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface $viiGiftCardEntityRepository,
        \Ewave\Vii\Service\Config\Config $config
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
                
                //Update 08/11/2020** 
                //Needs to execute redemption as long as it is execute Pre Auth 
//               if ($this->isAcceptAvailable($order, $entity, $giftCard)) { //here
//
//               }
                
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
