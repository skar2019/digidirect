<?php

namespace Marketplacer\BrandApi\Model\Stubs;

use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Model\Attribute\AttributeOptionHandler;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterfaceFactory;
use Marketplacer\BrandApi\Api\BrandRepositoryInterface;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;

class BrandRepositoryStub implements BrandRepositoryInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AttributeOptionHandler
     */
    protected $attributeOptionHandler;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;
    /**
     * @var MarketplacerBrandInterfaceFactory
     */
    protected $brandFactory;

    /**
     * BrandRepository constructor.
     * @param AttributeOptionHandler $attributeOptionHandler
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     * @param StoreManagerInterface $storeManager
     * @param AttributeOptionInterfaceFactory $attributeOptionFactory
     */
    public function __construct(
        AttributeOptionHandler $attributeOptionHandler,
        BrandAttributeRetrieverInterface $brandAttributeRetriever,
        StoreManagerInterface $storeManager,
        MarketplacerBrandInterfaceFactory $brandFactory
    ) {
        $this->attributeOptionHandler = $attributeOptionHandler;
        $this->brandAttributeRetriever = $brandAttributeRetriever;
        $this->storeManager = $storeManager;
        $this->brandFactory = $brandFactory;
    }

    /**
     * @param int $brandId
     * @param int | string | null $storeId
     * @return MarketplacerBrandInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getById($brandId, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $brandAttribute = $this->brandAttributeRetriever->getAttribute();
        $option = $this->attributeOptionHandler->getAttributeOptionById($brandAttribute, $brandId);
        if (!$option) {
            throw new NoSuchEntityException(__('The brand option with ID "%1" does not exist.', $brandId ?? ''));
        }

        $brandName = $option->getLabel();
        if ($option->getStoreLabels()) {
            foreach ($option->getStoreLabels() as $label) {
                if ($label->getLabel() && $label->getStoreId() == $storeId) {
                    $brandName = $label->getLabel();
                    break;
                }
            }
        }

        $brand = $this->brandFactory->create();
        $brand->setBrandId($brandId);
        $brand->setName($brandName);

        return $brand;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        throw new LocalizedException(
            __('This method is not implemented.'
                . ' To Use it please install Marketplacer_Brand module or ask your developers to implement your own solution.'
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function save($brand)
    {
        throw new LocalizedException(
            __('This method is not implemented.'
                . ' To Use it please install Marketplacer_Brand module or ask your developers to implement your own solution.'
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteById($brandId)
    {
        throw new LocalizedException(
            __('This method is not implemented.'
                . ' To Use it please install Marketplacer_Brand module or ask your developers to implement your own solution.'
            )
        );
    }
}
