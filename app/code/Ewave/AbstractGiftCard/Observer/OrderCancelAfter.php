<?php

namespace Ewave\AbstractGiftCard\Observer;

use Ewave\AbstractGiftCard\Model\AbstractGiftCardEntity;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GiftCardAccount\Model\Giftcardaccount;

class OrderCancelAfter extends \Magento\GiftCardAccount\Observer\ReturnFundsToStoreCredit
{
    /**
     * @var \Ewave\AbstractGiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     */
    protected $_giftCardAccountFactory;

    /**
     * @var \Ewave\AbstractGiftCard\Model\AbstractGiftCardEntityFactory $abstractGiftCardEntityFactory
     */
    protected $_abstractGiftCardEntityFactory;

    /**
     * @var \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface
     */
    protected $_abstractGiftCardEntityRepository;

    /**
     * OrderCancelAfter constructor.
     *
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param \Magento\CustomerBalance\Model\Balance $customerBalance
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ewave\AbstractGiftCard\Helper\Data $helper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     * @param \Ewave\AbstractGiftCard\Model\AbstractGiftCardEntityFactory $abstractGiftCardEntityFactory
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        \Magento\CustomerBalance\Model\Balance $customerBalance,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ewave\AbstractGiftCard\Helper\Data $helper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory,
        \Ewave\AbstractGiftCard\Model\AbstractGiftCardEntityFactory $abstractGiftCardEntityFactory,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
    ) {
        parent::__construct($giftCAHelper, $customerBalance, $storeManager);
        $this->_helper = $helper;
        $this->_giftCardAccountFactory = $giftcardaccountFactory;
        $this->_abstractGiftCardEntityFactory = $abstractGiftCardEntityFactory;
        $this->_abstractGiftCardEntityRepository = $abstractGiftCardEntityRepository;
    }

    /**
     * Set completion_date date
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isActive()) {
            return parent::execute($observer);
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $cards = $this->giftCAHelper->getCards($order);
        if (is_array($cards)) {
            $balance = 0;
            foreach ($cards as $card) {
                try {
                    $giftCardAccount = $this->_giftCardAccountFactory->create()
                        ->loadByCode($card[Giftcardaccount::CODE]);
                    $entity = $this->_abstractGiftCardEntityRepository->loadByGiftCardAccount($giftCardAccount);
                    $entityOrderData = $entity->getEntityOrderData($order->getEntityId());
                    if ($entityOrderData->getStatus() == AbstractGiftCardEntity::STATUS_HOLD
                        || $entityOrderData->getStatus() == AbstractGiftCardEntity::STATUS_ACCEPT
                    ) {
                        $service = $entity->getService();
                        $service->setStore($order->getStoreId());
                        $service->setOrder($order);
                        $service->validate()->cancel(
                            __('Order Cancelation'),
                            $entityOrderData->getToken(),
                            $entityOrderData->getAmount()
                        );
                        $entityOrderData->setStatus(AbstractGiftCardEntity::STATUS_CANCEL);
                        $this->_abstractGiftCardEntityRepository->saveEntityOrderData($entityOrderData);
                    }
                    continue;
                } catch (NoSuchEntityException $e) {
                    $balance += $card[\Magento\GiftCardAccount\Model\Giftcardaccount::BASE_AMOUNT];
                }
            }

            if ($balance > 0) {
                $this->customerBalance->setCustomerId(
                    $order->getCustomerId()
                )->setWebsiteId(
                    $this->storeManager->getStore($order->getStoreId())->getWebsiteId()
                )->setAmountDelta(
                    $balance
                )->setHistoryAction(
                    \Magento\CustomerBalance\Model\Balance\History::ACTION_REVERTED
                )->setOrder(
                    $order
                )->save();
            }
        }

        return $this;
    }
}
