<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Ui\DataProvider\Form\Banner;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\Repository\BannerRepository;
use Magento\Framework\App\RequestInterface;
use Amasty\BannerSlider\Model\Banner;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\BannerSlider\Model\ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var BannerRepository
     */
    private $bannerRepository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        PoolInterface $pool,
        RequestInterface $request,
        \Amasty\BannerSlider\Model\ImageProcessor $imageProcessor,
        BannerRepository $bannerRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $coreRegistry;
        $this->pool = $pool;
        $this->request = $request;
        $this->imageProcessor = $imageProcessor;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $result = parent::getData();
        /** @var Banner $current */
        $current = $this->coreRegistry->registry(BannerInterface::PERSIST_NAME);
        if ($current->getId()) {
            $data = $this->prepareData($current->getData());
            $result[$current->getId()] = $data;
        }

        return $result;
    }

    protected function prepareData(array $data): array
    {
        if ($storeId = (int)$this->request->getParam('store', Store::DEFAULT_STORE_ID)) {
            $data['store_id'] = $storeId;
        }
        if (isset($data[BannerInterface::IMAGE])) {
            $data[BannerInterface::IMAGE] = [
                [
                    'name' => $data[BannerInterface::IMAGE],
                    'url'  => $this->imageProcessor->getThumbnailUrl($data[BannerInterface::IMAGE])
                ]
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
