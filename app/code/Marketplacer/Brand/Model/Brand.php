<?php

namespace Marketplacer\Brand\Model;

use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Marketplacer\Base\Model\Attribute\AttributeOptionHandler;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Model\UrlProcessor\BrandProcessor;
use Marketplacer\Brand\Model\UrlProcessor\BrandProcessorFactory;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use Marketplacer\BrandApi\Model\MarketplacerBrand;

/**
 * @method unsRowId()
 */
class Brand extends MarketplacerBrand implements BrandInterface
{
    /**
     * @var AttributeOptionHandler
     */
    protected $attributeOptionHandler;

    /**
     * @var AttributeOptionInterface
     */
    protected $attributeOption;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriver;

    /**
     * @var BrandProcessor
     */
    protected $urlProcessor;

    /**
     * @var BrandProcessorFactory
     */
    protected $urlProcessorFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     * @param AttributeOptionHandler $attributeOptionHandler
     * @param BrandProcessorFactory $urlProcessorFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        BrandAttributeRetrieverInterface $brandAttributeRetriever,
        AttributeOptionHandler $attributeOptionHandler,
        BrandProcessorFactory $urlProcessorFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->attributeOptionHandler = $attributeOptionHandler;
        $this->brandAttributeRetriver = $brandAttributeRetriever;
        $this->urlProcessorFactory = $urlProcessorFactory;
    }

    /**
     * Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Brand::class);
    }

//    /**
//     * @inheritDoc
//     */
//    public function getId()
//    {
//        return $this->getData(BrandInterface::ID);
//    }
//
//    /**
//     * @inheritDoc
//     */
//    public function setId($id)
//    {
//        $this->setData(BrandInterface::ID, $id);
//        return $this;
//    }

    /**
     * @inheritDoc
     */
    public function getRowId()
    {
        return $this->_getData(BrandInterface::ROW_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRowId($rowId)
    {
        $this->setData(BrandInterface::ROW_ID, $rowId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->_getData(BrandInterface::STORE_ID);
    }

    /**
     * @param int $storeId
     * @return BrandInterface
     */
    public function setStoreId($storeId)
    {
        $this->setData(BrandInterface::STORE_ID, $storeId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptionId()
    {
        return $this->_getData(BrandInterface::OPTION_ID);
    }

    /**
     * Separate brand and option columns create for future usage. Now both values are always the same
     *
     * @param int $optionId
     * @return $this|BrandInterface
     */
    public function setOptionId($optionId)
    {
        $this->setData(BrandInterface::OPTION_ID, $optionId);
        $this->setBrandId($optionId);
        return $this;
    }

    /**
     * @return int|mixed|null
     */
    public function getStatus()
    {
        return $this->_getData(BrandInterface::STATUS);
    }

    /**
     * @param int $status
     * @return $this|BrandInterface
     */
    public function setStatus($status)
    {
        $this->setData(BrandInterface::STATUS, $status);
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return self::STATUS_ENABLED == $this->getStatus();
    }

    /**
     * @return int|mixed|null
     */
    public function getSortOrder()
    {
        return $this->_getData(BrandInterface::SORT_ORDER);
    }

    /**
     * @param int $sortOrder
     * @return $this|BrandInterface
     */
    public function setSortOrder($sortOrder)
    {
        $this->setData(BrandInterface::SORT_ORDER, $sortOrder);
        return $this;
    }

    /**
     * Get Brand name (option label)
     */
    public function getName()
    {
        if (!$this->hasData(BrandInterface::NAME)) {
            $this->setData(BrandInterface::NAME, $this->getLabelFromAttributeOption());
        }

        return $this->_getData(BrandInterface::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->setData(BrandInterface::NAME, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUrlKey()
    {
        return $this->_getData(BrandInterface::URL_KEY);
    }

    /**
     * @param string $urlKey
     * @return BrandInterface
     */
    public function setUrlKey($urlKey)
    {
        $this->validateUrlKey($urlKey);

        $this->setData(BrandInterface::URL_KEY, $urlKey);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitle()
    {
        return $this->_getData(BrandInterface::META_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setMetaTitle($metaTitle)
    {
        $this->setData(BrandInterface::META_TITLE, $metaTitle);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescription()
    {
        return $this->_getData(BrandInterface::META_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setMetaDescription($metaDescription)
    {
        $this->setData(BrandInterface::META_DESCRIPTION, $metaDescription);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(BrandInterface::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(BrandInterface::CREATED_AT, $createdAt);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(BrandInterface::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(BrandInterface::UPDATED_AT, $updatedAt);
        return $this;
    }

    /**
     * @return AttributeOptionInterface
     * @throws NoSuchEntityException
     */
    public function getAttributeOption()
    {
        if (null === $this->attributeOption) {
            $attribute = $this->brandAttributeRetriver->getAttribute();
            $this->attributeOption =
                $this->attributeOptionHandler->getAttributeOptionById($attribute, $this->getOptionId());
        }

        return $this->attributeOption;
    }

    /**
     * @return bool
     */
    public function hasAttributeOption()
    {
        return null !== $this->attributeOption;
    }

    /**
     * @param AttributeOptionInterface $attributeOption
     * @return $this
     */
    public function setAttributeOption(AttributeOptionInterface $attributeOption)
    {
        $this->attributeOption = $attributeOption;
        return $this;
    }

    /**
     * Clean url key
     * @param string $urlKey
     * @return string
     * @throws ValidatorException
     */
    public function getSanitizedUrlKey($urlKey)
    {
        return preg_replace('/[^0-9a-z\_]/', '-', strtolower(trim($urlKey)));
    }

    /**
     * Validate url key
     * @param string $urlKey
     * @return true If url key is valid
     * @throws ValidatorException
     */
    public function validateUrlKey($urlKey)
    {
        $isValid = $this->_validateUrlKey($urlKey);

        if (!$isValid) {
            throw new ValidatorException(__('Url key is not valid'));
        }

        return true;
    }

    /**
     * Validate url key
     *
     * @param string $urlKey
     * @return bool
     */
    protected function _validateUrlKey($urlKey)
    {
        if (!$urlKey) {
            return false;
        }

        return true;
    }

    /**
     * Process url rewrites
     *
     * @return $this
     * @throws NoSuchEntityException
     * @throws UrlAlreadyExistsException
     */
    public function processUrlRewrites()
    {
        $urlProcessor = $this->getUrlProcessor();
        $urlProcessor->processBrandUrlRewrites($this);
        return $this;
    }

    /**
     * Delete url rewrites
     *
     * @return $this
     */
    public function deleteUrlRewrites()
    {
        $urlProcessor = $this->getUrlProcessor();
        $urlProcessor->deleteUrlRewrites($this);
        return $this;
    }

    /**
     * Get Brand name (option label)
     */
    public function getLabelFromAttributeOption()
    {
        $label = null;
        if ($this->getAttributeOption()) {
            if ($this->getStoreId() == Store::DEFAULT_STORE_ID || !$this->getAttributeOption()->getStoreLabels()) {
                $label = $this->getAttributeOption()->getLabel();
            } else {
                foreach ($this->getAttributeOption()->getStoreLabels() as $storeLabel) {
                    if ($this->getStoreId() == $storeLabel->getStoreId()) {
                        $label = $storeLabel->getLabel();
                        break;
                    }
                }
            }
        }

        return $label;
    }

    /**
     * @return Brand
     * @throws ValidatorException
     */
    public function refreshUrlKey()
    {
        $optionLabel = $this->hasAttributeOption() ? $this->getLabelFromAttributeOption() : null;
        $brandName = $this->getName();

        if (!$optionLabel || strcmp($optionLabel, $brandName) !== 0) {
            $newUrlKey = $this->getSanitizedUrlKey($this->getName());
            $this->setUrlKey($newUrlKey);

            $this->setData('_regenerate_url', true);
        }

        return $this;
    }

    /**
     * @return BrandProcessor
     */
    protected function getUrlProcessor()
    {
        if (null === $this->urlProcessor) {
            $this->urlProcessor = $this->urlProcessorFactory->create();
        }

        return $this->urlProcessor;
    }

    /**
     * @return Brand
     * @throws NoSuchEntityException
     */
    protected function _afterLoad()
    {
        $optionId = $this->getOptionId();
        if ($optionId) {
            $attribute = $this->brandAttributeRetriver->getAttribute();
            $attributeOption = $this->attributeOptionHandler->getAttributeOptionById($attribute, $optionId);

            $this->setAttributeOption($attributeOption);
        }

        return parent::_afterLoad();
    }
}
