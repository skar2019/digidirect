<?php
namespace Ewave\ProductCalculator\Ui\DataProvider\Field\Form;

use Magento\Framework\App\Request\DataPersistorInterface;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Model\ResourceModel\Field\Collection;
use Ewave\ProductCalculator\Model\ResourceModel\Field\CollectionFactory;
use Ewave\ProductCalculator\Model\Media\ImageProcessorFactory;
use Ewave\ProductCalculator\Model\Media\ImageProcessor;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class DataProvider
 * @package Ewave\ProductCalculator\Ui\DataProvider\Field\Form
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const DATA_PERSISTOR_KEY = 'ewave_productcalculator_field';

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var ImageProcessorFactory
     */
    protected $imageProcessorFactory;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface $pool
     * @param ImageProcessorFactory $imageProcessorFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        ImageProcessorFactory $imageProcessorFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->pool = $pool;
        $this->imageProcessorFactory = $imageProcessorFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get meta from all modifiers
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $modifiers = $this->pool->getModifiersInstances();
        /** @var ModifierInterface $modifier */
        foreach ($modifiers as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var ImageProcessor $imageProcessor**/
        $imageProcessor = $this->imageProcessorFactory->create();
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $model->setData(
                FieldInterface::IMAGE_INFO_KEY,
                $imageProcessor->getImageInfo($model->getData(FieldInterface::IMAGE))
            );
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get(self::DATA_PERSISTOR_KEY);

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear(self::DATA_PERSISTOR_KEY);
        }

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->loadedData = $modifier->modifyData($this->loadedData ?? []);
        }

        return $this->loadedData;
    }
}
