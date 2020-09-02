<?php

namespace Ewave\Vii\Cron;

use Ewave\Vii\Service\Config\Config;
use Ewave\Vii\Model\ResourceModel\AbstractGiftCardEntity;
use Ewave\AI\Helper\Queue as QueueHelper;
use Ewave\Vii\Model\Processor\Quote\CleanupProcess;
use Magento\Quote\Api\Data\CartInterface;
use Magento\GiftCardAccount\Model\Giftcardaccount;

class QuoteCleanup
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var AbstractGiftCardEntity
     */
    protected $abstractGiftCardEntityResource;

    /**
     * @var QueueHelper
     */
    protected $queue;

    /**
     * QuoteCleanup constructor.
     * @param Config $config
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param AbstractGiftCardEntity $abstractGiftCardEntity
     * @param QueueHelper $queue
     */
    public function __construct(
        Config $config,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        AbstractGiftCardEntity $abstractGiftCardEntity,
        QueueHelper $queue
    ) {
        $this->config = $config;
        $this->abstractGiftCardEntityResource = $abstractGiftCardEntity;
        $this->quoteCollectionFactory = $collectionFactory;
        $this->queue = $queue;
    }

    /**
     * Clean expired quotes (cron process)
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->config->isActive()) {
            return;
        }

        $expireDate = $this->config->getExpireDate();
        if (!$expireDate) {
            return;
        }

        $expiredGiftCardQuoteIds = $this->abstractGiftCardEntityResource->getExpiredEntityQuoteIds($expireDate);

        if (empty($expiredGiftCardQuoteIds)) {
            return;
        }

        /** @var $quotes \Magento\Quote\Model\ResourceModel\Quote\Collection */
        $quotes = $this->quoteCollectionFactory->create();
        $quotes->addFieldToFilter(CartInterface::KEY_ENTITY_ID, ['in' => $expiredGiftCardQuoteIds]);
        $quotes->addFieldToFilter(CartInterface::KEY_IS_ACTIVE, 1);
        $quotes->addFieldToFilter(Giftcardaccount::BASE_GIFT_CARDS_AMOUNT, ['gt' => 0]);

        $isQueued = [];
        foreach ($quotes as $quote) {
            $this->pushToQueue($quote);
            $isQueued[] = $quote->getId();
        }

        if (!empty($isQueued)) {
            $this->abstractGiftCardEntityResource->updateEntityQuoteData(
                ['is_queued' => 1],
                [
                    'quote_id IN (?)' => implode(',', $isQueued),
                    'start_date < ?' => $expireDate
                ]
            );
        }
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function pushToQueue(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        $this->queue->createQueueItem(
            CleanupProcess::PROCESS_CODE,
            QueueHelper::getSerializedProcessData(
                ['quote' => $quote->getEntityId()]
            ),
            self::class
        );
    }
}
