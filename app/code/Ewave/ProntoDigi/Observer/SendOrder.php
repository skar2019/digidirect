<?php

namespace Ewave\ProntoDigi\Observer;

use Ewave\AI\Model\Engine\EngineFactory;
use Ewave\ProntoDigi\ProntoApi\Orders\Post;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Ewave\ProntoDigi\ProntoApi\Orders\Post\OrderPostExecutor;
use Psr\Log\LoggerInterface;

class SendOrder implements ObserverInterface
{
    /**
     * @var OrderPostExecutor
     */
    protected $orderPostExecutor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SendOrder constructor.
     * @param OrderPostExecutor $orderPostExecutor
     * @param LoggerInterface $logger
     */
    public function __construct(OrderPostExecutor $orderPostExecutor, LoggerInterface $logger)
    {
        $this->orderPostExecutor = $orderPostExecutor;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getOrder();
            $this->orderPostExecutor->pushToQueue($order);
        } catch (\Throwable $e) {
            if ($e->getCode() != OrderPostExecutor::FAILED_VALIDATION_CODE) {
                $this->logger->warning($e->__toString());
            }
        }
    }
}
