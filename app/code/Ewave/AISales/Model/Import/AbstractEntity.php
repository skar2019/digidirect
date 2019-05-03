<?php
namespace Ewave\AISales\Model\Import;

use Ewave\AI\Model\Lib\Entity\Import\ImportAbstract;
use Ewave\AI\Model\Logger\LoggerInterface;
use Ewave\AI\Model\Lib\Validator\Validate as Validator;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\DataObject;

abstract class AbstractEntity extends ImportAbstract
{
    /**
     * @var AbstractProcessor
     */
    protected $processor;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var string
     */
    protected $eventPrefix = 'ewave_ai_sales_import_';

    /**
     * ImportAbstract constructor.
     * @param Validator $beforeSaveValidator
     * @param Validator $beforeUpdateValidator
     * @param LoggerInterface $logger
     * @param AbstractProcessor $processor
     * @param EventManager $eventManager
     * @param array $preparers
     */
    public function __construct(
        Validator $beforeSaveValidator,
        Validator $beforeUpdateValidator,
        LoggerInterface $logger,
        AbstractProcessor $processor,
        EventManager $eventManager,
        array $preparers = []
    ) {
        parent::__construct(
            $beforeSaveValidator,
            $beforeUpdateValidator,
            $logger,
            $preparers
        );
        $this->processor = $processor;
        $this->eventManager = $eventManager;
    }

    /**
     * @param array $entity
     * @param bool $updateOnDuplicate
     * @return bool
     */
    protected function _save(array $entity, $updateOnDuplicate = true)
    {
        return $this->_saveBunch([$entity], $updateOnDuplicate);
    }

    /**
     * @param array $entities
     * @param bool $updateOnDuplicate
     * @return bool
     */
    protected function _saveBunch(array $entities, $updateOnDuplicate = true)
    {
        $this->eventDispatch('save_before', ['entities' => $entities, 'updateOnDuplicate' => $updateOnDuplicate]);
        $result = $this->processor->save($entities, $updateOnDuplicate);
        $this->eventDispatch('save_after', [
            'entities' => $this->processor->getProcessedEntities(),
            'updateOnDuplicate' => $updateOnDuplicate,
            'result' => $result
        ]);
        $this->eventDispatch('after');
        return $result;
    }

    /**
     * @param array $entity
     * @return bool
     */
    protected function _update(array $entity)
    {
        return $this->_updateBunch([$entity]);
    }

    /**
     * @param array $entities
     * @return bool
     */
    protected function _updateBunch(array $entities)
    {
        $this->eventDispatch('update_before', ['entities' => $entities]);
        $result = $this->processor->update($entities);
        $this->eventDispatch('update_after', [
            'entities' => $this->processor->getProcessedEntities(),
            'result' => $result
        ]);
        $this->eventDispatch('after');
        return $result;
    }

    /**
     * @param string $incrementId
     * @param int $storeId
     * @return bool
     */
    public function delete($incrementId, $storeId)
    {
        $this->eventDispatch('delete_before', ['incrementId' => $incrementId, 'storeId' => $storeId]);
        $result = $this->processor->delete($incrementId, $storeId);
        $this->eventDispatch('delete_after', [
            'incrementId' => $incrementId,
            'storeId' => $storeId,
            'result' => $result
        ]);
        $this->eventDispatch('after');
        return $result;
    }

    /**
     * @param string $key
     * @param array $data
     * @return void
     */
    protected function eventDispatch($key, array $data = [])
    {
        $this->eventManager->dispatch($this->eventPrefix . $key, $data);
    }
}
