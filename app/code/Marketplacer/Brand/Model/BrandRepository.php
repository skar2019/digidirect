<?php

namespace Marketplacer\Brand\Model;

use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Model\Attribute\AttributeOptionHandler;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Api\Data\BrandCollectionInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Api\Data\BrandInterfaceFactory;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandSearchResultsInterfaceFactory;
use Marketplacer\Brand\Model\Brand\BrandDataToOptionSetter;
use Marketplacer\Brand\Model\Brand\Validator as BrandValidator;
use Marketplacer\Brand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * @var \Marketplacer\Brand\Model\ResourceModel\Brand
     */
    protected $brandResource;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BrandInterfaceFactory
     */
    protected $brandFactory;

    /**
     * @var BrandValidator
     */
    protected $brandValidator;

    /**
     * @var BrandDataToOptionSetter
     */
    protected $brandDataToOptionSetter;

    /**
     * @var array
     */
    protected $imageInfoByUrl = [];

    /**
     * @var AttributeOptionInterfaceFactory
     */
    protected $attributeOptionFactory;

    /**
     * @var AttributeOptionHandler
     */
    protected $attributeOptionHandler;

    /**
     * @var BrandCollectionFactory
     */
    protected $brandCollectionFactory;

    /**
     * @var MarketplacerBrandSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * BrandRepository constructor.
     * @param ResourceModel\Brand $brandResource
     * @param BrandInterfaceFactory $brandFactory
     * @param AttributeOptionHandler $attributeOptionHandler
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     * @param StoreManagerInterface $storeManager
     * @param AttributeOptionInterfaceFactory $attributeOptionFactory
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param BrandValidator $brandValidator
     * @param BrandDataToOptionSetter $brandDataToOptionSetter
     * @param MarketplacerBrandSearchResultsInterfaceFactory $brandSearchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Marketplacer\Brand\Model\ResourceModel\Brand $brandResource,
        BrandInterfaceFactory $brandFactory,
        AttributeOptionHandler $attributeOptionHandler,
        BrandAttributeRetrieverInterface $brandAttributeRetriever,
        StoreManagerInterface $storeManager,
        AttributeOptionInterfaceFactory $attributeOptionFactory,
        BrandCollectionFactory $brandCollectionFactory,
        BrandValidator $brandValidator,
        BrandDataToOptionSetter $brandDataToOptionSetter,
        MarketplacerBrandSearchResultsInterfaceFactory $brandSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        LoggerInterface $logger
    ) {
        $this->brandResource = $brandResource;
        $this->brandFactory = $brandFactory;
        $this->attributeOptionHandler = $attributeOptionHandler;
        $this->brandAttributeRetriever = $brandAttributeRetriever;
        $this->storeManager = $storeManager;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->brandValidator = $brandValidator;
        $this->brandDataToOptionSetter = $brandDataToOptionSetter;
        $this->searchResultsFactory = $brandSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getById($brandId, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        /**
         * @var $brand BrandInterface
         */
        $brandCollection = $this->brandCollectionFactory->create();
        $brandCollection->addBrandIdToFilter($brandId);
        $brandCollection->addStoreIdToFilter($storeId, true);
        $brandCollection->setOrder(BrandInterface::STORE_ID, 'DESC');
        //brand might have several records on storeview or on global level
        $brandCollection->setCurPage(1)->setPageSize(1);
        $brand = $brandCollection->getFirstItem();

        if (!$brand || !$brand->getRowId()) {
            throw new NoSuchEntityException(__('The brand with ID "%1" does not exist.', $brandId ?? ''));
        }

        // brand exist, but not for requested store (for default) so create virtual copy
        if ($brand->getStoreId() != $storeId) {
            $brand = clone $brand;
            $brand->unsRowId();
            $brand->setStoreId($storeId);
        }

        return $brand;
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $brandIds = [], $storeId = null)
    {
        if (!$brandIds) {
            return [];
        }

        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        /**
         * @var $brand BrandInterface
         */
        $brandCollection = $this->brandCollectionFactory->create();
        $brandCollection->addStoreIdToFilter($storeId, true);
        $brandCollection->addBrandIdToFilter(['in' => $brandIds]);

        $brandCollection->setOrder(BrandInterface::SORT_ORDER, 'ASC');
        $brandCollection->setOrder(BrandInterface::STORE_ID, 'DESC');

        return $this->getStoreItemsFromCollection($brandCollection, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getAllDisplayedBrands($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        /**
         * @var $brand BrandInterface
         */
        $brandCollection = $this->brandCollectionFactory->create();
        $brandCollection->addStoreIdToFilter($storeId, true);
        $brandCollection->addStatusActiveToFilter();

        $brandCollection->setOrder(BrandInterface::SORT_ORDER, 'ASC');
        $brandCollection->setOrder(BrandInterface::STORE_ID, 'DESC');

        return $this->getStoreItemsFromCollection($brandCollection, $storeId);
    }

    /**
     * @param BrandCollectionInterface $brandCollection
     * @param $storeId
     * @return array
     */
    protected function getStoreItemsFromCollection(BrandCollectionInterface $brandCollection, $storeId)
    {
        //seller might have several records on storeview or on global level
        $resultBrands = [];
        foreach ($brandCollection->getItems() as $brand) {
            if ($brand->getStoreId() == $storeId) {
                $resultBrands[$brand->getBrandId()] = $brand;
            } elseif (!isset($resultBrands[$brand->getBrandId()])) {
                // seller exist, but not for requested store (for default) so create virtual copy
                $brand = clone $brand;
                $brand->unsRowId();
                $brand->setStoreId($storeId);
                $resultBrands[$brand->getBrandId()] = $brand;
            }
        }

        return $resultBrands;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getAllBrandIds()
    {
        return $this->brandResource->getAllBrandIds();
    }

    /**
     * @param int $brandId
     * @param int | null $storeId
     * @return BrandInterface[]
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAllStoreRecordsById($brandId)
    {
        /**
         * @var $brand BrandInterface
         */
        $storeIds = array_keys($this->storeManager->getStores(true, false));
        $brandCollection = $this->brandCollectionFactory->create();
        $brandCollection->addBrandIdToFilter($brandId);
        $brandCollection->addStoreIdToFilter(['in' => $storeIds], true);
        //brand might have several records on storeview or on global level

        $brandsByStoreId = [];
        foreach ($brandCollection as $brand) {
            $brandsByStoreId[$brand->getStoreId()] = $brand;
        }

        if (!$brandsByStoreId) {
            throw new NoSuchEntityException(__('The brand with ID "%1" does not exist.', $brandId ?? ''));
        }

        return $brandsByStoreId;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->brandCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        if (!$collection->hasFlag('_store_filter_applied')) {
            $collection->addStoreIdToFilter($this->storeManager->getStore()->getId());
        }

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setBrands($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save($brand)
    {
        if (!$brand->hasStoreId()) {
            $brand->setStoreId($this->storeManager->getStore()->getId());
        }

        $brandAttribute = $this->brandAttributeRetriever->getAttribute();

        if (!$brand->getOptionId()) { //new brand
            $this->brandValidator->validate($brand);

            $attributeOption = $this->attributeOptionHandler->createAttributeOption();
            $attributeOption->setLabel($brand->getName());

            $this->brandDataToOptionSetter->setFromBrand($brand, $attributeOption);

            if (!$this->attributeOptionHandler->isAdminLabelUnique($brandAttribute, $attributeOption)) {
                throw new CouldNotSaveException(__('Brand with this name already exists.'));
            }

            $brandStoreId = $brand->getStoreId();
            $brand->setStoreId(Store::DEFAULT_STORE_ID);

            $this->brandResource->beginTransaction();
            try {
                $this->attributeOptionHandler->saveAttributeOption($brandAttribute, $attributeOption);
                if (!$attributeOption->getValue()) {
                    throw new LocalizedException(__('Unable to save Brand attribute option'));
                }
                $brand->setOptionId($attributeOption->getValue());

                $brand->refreshUrlKey();
                $this->brandResource->save($brand);

                //if first creation was on store level, create base record and copy on store level to match options
                if ($brandStoreId != Store::DEFAULT_STORE_ID) {
                    $brand->setStoreId($brandStoreId);
                    $brand->unsRowId();
                    $brand->refreshUrlKey();

                    $this->brandResource->save($brand);
                }

                $this->brandResource->commit();
            } catch (LocalizedException $exception) {
                $this->brandResource->rollBack();
                throw $exception;
            } catch (Throwable $exception) {
                $this->brandResource->rollBack();
                $this->logger->critical($exception);
                throw new LocalizedException(__('Unable to create brand'));
            }
        } else {
            //existing brand
            $optionId = $brand->getOptionId();
            if (!$optionId || !$this->isBrandOptionExist($optionId)) {
                throw new NoSuchEntityException(__('Brand attribute option with id = %1 not found.', $optionId));
            }

            $this->brandValidator->validate($brand, true);

            $brand->refreshUrlKey();

            $attributeOption = $brand->getAttributeOption();
            $this->brandDataToOptionSetter->setFromBrand($brand, $attributeOption);

            if (!$this->attributeOptionHandler->isAdminLabelUnique($brandAttribute, $attributeOption)) {
                throw new CouldNotSaveException(__('Brand with this name already exists.'));
            }

            $this->brandResource->beginTransaction();
            try {
                $this->attributeOptionHandler->saveAttributeOption($brandAttribute, $attributeOption);

                $this->brandResource->save($brand);

                $this->brandResource->commit();
            } catch (LocalizedException $exception) {
                $this->brandResource->rollBack();
                throw $exception;
            } catch (Throwable $exception) {
                $this->brandResource->rollBack();
                $this->logger->critical($exception);
                throw new LocalizedException(__('Unable to update the brand with ID "%1"', $brand->getBrandId()));
            }
        }

        return $brand;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($brandId)
    {
        $brand = $this->getById($brandId, Store::DEFAULT_STORE_ID);

        $brandAttribute = $this->brandAttributeRetriever->getAttribute();

        $this->brandResource->beginTransaction();
        try {
            if ($brand->getOptionId()
                && $this->attributeOptionHandler->getAttributeOptionById($brandAttribute, $brand->getOptionId())
            ) {
                $this->attributeOptionHandler->deleteOptionById($brandAttribute, $brand->getOptionId());
            }

            $this->brandResource->delete($brand);

            $this->brandResource->commit();
        } catch (LocalizedException $exception) {
            $this->brandResource->rollBack();
            throw $exception;
        } catch (Throwable $exception) {
            $this->brandResource->rollBack();
            $this->logger->critical($exception);
            throw new LocalizedException(__('Unable to delete the brand with ID "%1"', $brandId));
        }

        return true;
    }

    /**
     * @param int $optionId
     * @return bool
     * @throws LocalizedException
     */
    public function isBrandOptionExist($optionId)
    {
        $brandAttribute = $this->brandAttributeRetriever->getAttribute();
        return $this->attributeOptionHandler->isAttributeOptionIdExist($brandAttribute, $optionId);
    }
}
