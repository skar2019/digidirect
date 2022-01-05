<?php

namespace Digidirect\AbstractGiftCard\Observer;

use Digidirect\AbstractGiftCard\Exception\BlockOrderPlaceException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\GiftCardAccount\Model\Giftcardaccount;
use Digidirect\AbstractGiftCard\Helper\Data;
use Digidirect\AbstractGiftCard\Model\AbstractGiftCardEntity;

class OrderPlaceBefore implements ObserverInterface
{
    /**
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $giftCAHelper;

    /**
     * @var \Digidirect\AbstractGiftCard\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     */
    protected $giftCardAccountFactory;

    /**
     * @var \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface
     */
    protected $abstractGiftCardEntityRepository;

    protected  $logger;
    /**
     * QuoteSubmitBefore constructor.
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param Data $helper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     * @param \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        \Digidirect\AbstractGiftCard\Helper\Data $helper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory,
        \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository,
        \Digidirect\CustomLogGC\Logger\Logger $logger
    ) {
        $this->giftCAHelper = $giftCAHelper;
        $this->helper = $helper;
        $this->giftCardAccountFactory = $giftcardaccountFactory;
        $this->abstractGiftCardEntityRepository = $abstractGiftCardEntityRepository;
        $this->logger = $logger;
    }

    /**
     * @see \Magento\GiftCardAccount\Observer\ProcessOrderPlace::execute()
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order $order
         */
        if (!$this->helper->isActive()) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        $cards = $this->giftCAHelper->getCards($order);
        if (empty($cards)) {
            $this->logger->info('Empty cards');
            return;
        }

        $abstractGiftCardEntities = [];

        foreach ($cards as $giftCard) {
            try {
                $giftCardAccount = $this->giftCardAccountFactory->create()
                    ->loadByCode($giftCard[Giftcardaccount::CODE]);

                $entity = $this->abstractGiftCardEntityRepository->loadByGiftCardAccount($giftCardAccount);
                if ($giftCard[Giftcardaccount::CODE] != $entity->getCode()) {
                    continue;
                }

                $amount = $giftCard[Giftcardaccount::AUTHORIZED];

                try {
                    $service = $entity->getService();
                    $service->setStore($order->getStoreId());
                    $service->setOrder($order);
                    $service->validate()->hold($amount);
                    $this->logger->info('Gift card amount '.$amount);
                } catch (\Exception $e) {
                    $this->logger->info('Error '.$giftCard[Giftcardaccount::CODE]);
                    throw new BlockOrderPlaceException(
                        __('The requested Gift Card (%1) is not available.', $giftCard[Giftcardaccount::CODE])
                    );
                }

                $this->logger->info('Gift card token '.$service->getLastToken());
                $entity->setToken($service->getLastToken());
                $entity->setAmount($amount);
                $entity->setStatus(AbstractGiftCardEntity::STATUS_HOLD);

                $abstractGiftCardEntities[$giftCard[Giftcardaccount::CODE]] = [
                    'gift_card_account' => $giftCardAccount,
                    'entity' => $entity,
                ];
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        $order->setData('abstract_gift_card_entities', $abstractGiftCardEntities);
    }
}
