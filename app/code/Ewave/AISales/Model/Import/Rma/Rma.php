<?php
namespace Ewave\AISales\Model\Import\Rma;

use Ewave\AI\Model\Logger\LoggerInterface;
use Ewave\AI\Model\Lib\Validator\Validate as Validator;
use Ewave\AISales\Model\Import\AbstractEntity;
use Ewave\AISales\Model\Import\Rma\Model\Processor;
use Ewave\AISales\Model\Import\Rma\Model\ProcessorFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Rma extends AbstractEntity implements RmaInterface
{
    const ENTITY_NAME = 'rma';

    /**
     * @var string
     */
    protected $eventPrefix = 'ewave_ai_sales_rma_import_';

    /**
     * ImportAbstract constructor.
     * @param Validator $beforeSaveValidator
     * @param Validator $beforeUpdateValidator
     * @param LoggerInterface $logger
     * @param ProcessorFactory $processorFactory
     * @param EventManager $eventManager
     * @param array $preparers
     */
    public function __construct(
        Validator $beforeSaveValidator,
        Validator $beforeUpdateValidator,
        LoggerInterface $logger,
        ProcessorFactory $processorFactory,
        EventManager $eventManager,
        array $preparers = []
    ) {
        /** @var Processor $processor */
        $processor = $processorFactory->create([
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
