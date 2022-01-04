<?php

namespace Digidirect\AbstractGiftCard\Observer;

use Digidirect\AbstractGiftCard\Model\AbstractGiftCardEntity;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GiftCardAccount\Model\Giftcardaccount;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var \Digidirect\AbstractGiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $_giftCAHelper;

    /**
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     */
    protected $_giftCardAccountFactory;

    /**
     * @var \Digidirect\AbstractGiftCard\Model\AbstractGiftCardEntityFactory $abstractGiftCardEntityFactory
     */
    protected $_abstractGiftCardEntityFactory;

    /**
     * @var \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface
     */
    protected $_abstractGiftCardEntityRepository;

    /**
     * OrderSaveAfter constructor.
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param \Digidirect\AbstractGiftCard\Helper\Data $helper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     * @param \Digidirect\AbstractGiftCard\Model\AbstractGiftCardEntityFactory $abstractGiftCardEntityFactory
     * @param \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        \Digidirect\AbstractGiftCard\Helper\Data $helper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory,
        \Digidirect\AbstractGiftCard\Model\AbstractGiftCardEntityFactory $abstractGiftCardEntityFactory,
        \Digidirect\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository,
        \Digidirect\CustomLogGC\Logger\Logger $logger
    ) {
        $this->_helper = $helper;
        $this->_giftCAHelper = $giftCAHelper;
        $this->_giftCardAccountFactory = $giftcardaccountFactory;
        $this->_abstractGiftCardEntityFactory = $abstractGiftCardEntityFactory;
        $this->_abstractGiftCardEntityRepository = $abstractGiftCardEntityRepository;
        $this->logger = $logger;
    }

    /**
     * Execute accept command for external gift cards
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var $order Order
         */
        $order = $observer->getEvent()->getData('order');
        if (!$this->_helper->isActive($order->getStoreId())) {
            return;
        }

        $isAcceptForPaid = $this->_helper->isAcceptOnlyForPaidOrders();
        $storeData = $order->getStoredData();
        $dbState = $storeData[OrderInterface::STATE] ?? null;
        $state = $order->getState();
        if (empty($storeData)) {
            $this->logger->info('Empty storeData');
            return;
        }

        if ($dbState == $state && !$isAcceptForPaid) { //state was not changed
            $this->logger->info('dbState - '.$dbState.' : isAcceptForPaid - '.$isAcceptForPaid);
             return;
        }

        if ($isAcceptForPaid
            && $storeData[OrderInterface::BASE_TOTAL_DUE] == $order->getBaseTotalDue()
            && $order->getBaseTotalDue() != 0
        ) { // order wasn't paid
            $this->logger->info('order was not paid');
            return;
        }

        if (!in_array($state, [Order::STATE_PROCESSING, Order::STATE_COMPLETE])) { //accept only for these states
            $this->logger->info('order state is '.$state);
            return;
        }

        $cards = $this->_giftCAHelper->getCards($order);
        if (!is_array($cards) || empty($cards)) { //there is no abstract gift card
            $this->logger->info('there is no abstract gift card');
            return;
        }

        foreach ($cards as $card) {
            try {
                $giftCardAccount = $this->_giftCardAccountFactory->create()
                    ->loadByCode($card[Giftcardaccount::CODE]);
                $entity = $this->_abstractGiftCardEntityRepository->loadByGiftCardAccount($giftCardAccount);
                $entityOrderData = $entity->getEntityOrderData($order->getEntityId());

                if ($entityOrderData->getStatus() == AbstractGiftCardEntity::STATUS_HOLD) {
                    $service = $entity->getService();
                    $service->setStore($order->getStoreId());
                    $service->setOrder($order);
                    $service->validate()->accept($entityOrderData->getAmount(), $entityOrderData->getToken());
                    $entityOrderData->setStatus(AbstractGiftCardEntity::STATUS_ACCEPT);
                    $this->_abstractGiftCardEntityRepository->saveEntityOrderData($entityOrderData);
                }
            } catch (NoSuchEntityException $e) {
                continue;
            } catch (\Throwable $e) {
                $this->logger->info('Giftcard acceptance failed '.$e->getMessage());
                throw new LocalizedException(__('Giftcard acceptance failed: %1', $e->getMessage()), $e);
            }
        }
    }
}
