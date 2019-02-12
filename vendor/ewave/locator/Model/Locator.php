<?php

namespace Ewave\Locator\Model;

use Ewave\Locator\Model\Config\Data;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;

class Locator
{
    /**
     * @var Data
     */
    protected $configData;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var DataObject
     */
    protected $resultObject;

    /**
     * Locator constructor.
     * @param Data $configData
     * @param ObjectManagerInterface $objectManager
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Data $configData,
        ObjectManagerInterface $objectManager,
        ManagerInterface $eventManager
    ) {
        $this->configData = $configData;
        $this->objectManager = $objectManager;
        $this->eventManager = $eventManager;
        $this->resultObject = new DataObject();
    }

    /**
     * @param array $data
     * @param array $searchParams
     * @return array
     * @throws \Exception
     */
    public function getEntities(array $data = [], $searchParams = [])
    {
        $entities = $this->configData->getEntities(array_keys($data));
        foreach ($entities as $key => $value) {
            if (!$this->resultObject->hasData($key)) {
                $items = $this->objectManager->create($value['instance'])->process(
                    $data[$key],
                    $searchParams
                );
                $this->resultObject->setData($key, $items);
            }

        }
        $this->eventManager->dispatch(
            'ewave_locator_entites_after_load',
            ['result_object' => $this->resultObject]
        );
        return $this->resultObject->getData();
    }
}
