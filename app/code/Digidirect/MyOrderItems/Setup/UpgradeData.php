<?php

namespace Digidirect\MyOrderItems\Setup;

use Digidirect\MyOrderItems\Api\OrderItemStateRepositoryInterface;
use Digidirect\MyOrderItems\Model\ResourceModel\OrderItemState\CollectionFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UpgradeData
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CollectionFactory
     */
    protected $orderItemStateCollectionFactory;

    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderItemRepositiry;

    /**
     * @var OrderItemStateRepositoryInterface
     */
    protected $orderItemStateRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * UpgradeData constructor.
     * @param CollectionFactory $orderItemStateCollectionFactory
     * @param OrderItemRepositoryInterface $orderItemRepositiry
     * @param OrderItemStateRepositoryInterface $orderItemStateRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $orderItemStateCollectionFactory,
        OrderItemRepositoryInterface $orderItemRepositiry,
        OrderItemStateRepositoryInterface $orderItemStateRepository,
        LoggerInterface $logger
    ) {
        $this->orderItemStateCollectionFactory = $orderItemStateCollectionFactory;
        $this->orderItemRepositiry = $orderItemRepositiry;
        $this->orderItemStateRepository = $orderItemStateRepository;
        $this->logger = $logger;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->updateDateOfPurchaseColumnDate();
        }
    }

    /**
     * @return void
     */
    protected function updateDateOfPurchaseColumnDate()
    {
        $collection = $this->orderItemStateCollectionFactory->create()
            ->addFieldToFilter('date_of_purchase', ['null' => true]);

        foreach ($collection as $orderItemState) {
            try {
                $orderItem = $this->orderItemRepositiry->get($orderItemState->getSalesItemId());
                $orderItemState->setData('date_of_purchase', $orderItem->getCreatedAt());
                $this->orderItemStateRepository->save($orderItemState);
            } catch (\Exception $exception) {
                $this->logger->error(
                    __(
                        'Can\'t orderItemState %1. Error: %2',
                        $orderItemState->getSalesItemId(),
                        $exception->getMessage()
                    )
                );
            }
        }
    }
}
