<?php
namespace Ewave\AISales\Model\Import\Order;

use Ewave\AISales\Model\Import\AbstractEntity;
use Ewave\AI\Model\Logger\LoggerInterface;
use Ewave\AI\Model\Lib\Validator\Validate as Validator;
use Ewave\AISales\Model\Import\Order\Model\Processor;
use Ewave\AISales\Model\Import\Order\Model\ProcessorFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Order extends AbstractEntity implements OrderInterface
{
    const ENTITY_NAME = 'order';

    /**
     * @var string
     */
    protected $eventPrefix = 'ewave_ai_sales_orders_import_';

    /**
     * ImportAbstract constructor.
     * @param Validator $beforeSaveValidator
     * @param Validator $beforeUpdateValidator
     * @param LoggerInterface $logger
     * @param ProcessorFactory $orderProcessorFactory
     * @param EventManager $eventManager
     * @param array $preparers
     */
    public function __construct(
        Validator $beforeSaveValidator,
        Validator $beforeUpdateValidator,
        LoggerInterface $logger,
        ProcessorFactory $orderProcessorFactory,
        EventManager $eventManager,
        array $preparers = []
    ) {
        /** @var Processor $processor */
        $processor = $orderProcessorFactory->create([
            'logger' => $logger
        ]);
        parent::__construct(
            $beforeSaveValidator,
            $beforeUpdateValidator,
            $logger,
            $processor,
            $eventManager,
            $preparers
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::ENTITY_NAME;
    }
}
