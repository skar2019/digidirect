<?php
namespace Digidirect\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Psr\Log\LoggerInterface;

class ChangeOrderEmailTemplateObserver implements ObserverInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        /** @var OrderIdentity $identity */
        $identity = $observer->getData('identity');
        if (!$identity) {
            return;
        }

        // Force template ID 69 for all orders
        $identity->setTemplateId(69);

        $this->logger->info('Order email template forced to 69', [
            'template_id' => $identity->getTemplateId(),
            'store_id'    => $identity->getStore()->getId()
        ]);
    }
}
