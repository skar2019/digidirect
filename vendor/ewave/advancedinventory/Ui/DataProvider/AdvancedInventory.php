<?php
namespace Ewave\AdvancedInventory\Ui\DataProvider;

use Ewave\AdvancedInventory\Model\ResourceModel\StockItem\Collection;
use Ewave\AdvancedInventory\Model\ResourceModel\StockItem\CollectionFactory;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Ui\DataProvider\AbstractDataProvider;

class AdvancedInventory extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
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

        $items = $this->collection->getItems();
        foreach ($items as $model) {
            /** @var  StockItemInterface|DataObject $model */
            $model->setMinSaleQty((float)$model->getMinSaleQty());
            if (!$model->getIsQtyDecimal()) {
                $model->setQty((int)$model->getQty());
                $model->setMinSaleQty((int)$model->getMinSaleQty());
            }
            $this->loadedData[$model->getId()] = $model->getData();
            $this->addAdditionData($model);
        }

        $data = $this->dataPersistor->get('ewave_advancedpricing');
        if (!empty($data)) {
            /** @var  StockItemInterface|DataObject $model */
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getItemId()] = $model->getData();
            $this->dataPersistor->clear('ewave_advancedpricing');
        }

        if (empty($this->loadedData) && $this->getRequestProductId()) {
            /** @var  StockItemInterface|DataObject $model */
            $model = $this->collection->getNewEmptyItem();
            $this->loadedData[$model->getItemId()] = $model->getData();
            $this->addAdditionData($model);
        }

        return $this->loadedData;
    }

    /**
     * @param DataObject|StockItemInterface $model
     * @return void
     */
    protected function addAdditionData(DataObject $model)
    {
        $this->loadedData[$model->getItemId()][StockItemInterface::ITEM_ID] = $model->getItemId();
        $this->loadedData[$model->getItemId()][StockItemInterface::STOCK_ID] =
            $model->getData(StockItemInterface::STOCK_ID) ?: $this->getRequestStockId();
        $this->loadedData[$model->getItemId()][StockItemInterface::PRODUCT_ID] =
            $model->getData(StockItemInterface::PRODUCT_ID) ?: $this->getRequestProductId();
        $this->loadedData[$model->getItemId()]['back_to'] = $this->request->getParam('back_to');
    }

    /**
     * @return int
     */
    public function getRequestProductId()
    {
        return (int)$this->request->getParam(StockItemInterface::PRODUCT_ID);
    }

    /**
     * @return int
     */
    public function getRequestStockId()
    {
        return (int)$this->request->getParam(StockItemInterface::STOCK_ID);
    }
}
