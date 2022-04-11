<?php

namespace Marketplacer\SellerApi\Model\Stubs;

use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Model\Attribute\AttributeOptionHandler;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterfaceFactory;
use Marketplacer\SellerApi\Api\SellerRepositoryInterface;
use Marketplacer\SellerApi\Api\SellerAttributeRetrieverInterface;

class SellerRepositoryStub implements SellerRepositoryInterface
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
     * @var SellerAttributeRetrieverInterface
     */
    protected $sellerAttributeRetriever;
    /**
     * @var MarketplacerSellerInterfaceFactory
     */
    protected $sellerFactory;

    /**
     * SellerRepository constructor.
     * @param AttributeOptionHandler $attributeOptionHandler
     * @param SellerAttributeRetrieverInterface $sellerAttributeRetriever
     * @param StoreManagerInterface $storeManager
     * @param AttributeOptionInterfaceFactory $attributeOptionFactory
     */
    public function __construct(
        AttributeOptionHandler $attributeOptionHandler,
        SellerAttributeRetrieverInterface $sellerAttributeRetriever,
        StoreManagerInterface $storeManager,
        MarketplacerSellerInterfaceFactory $sellerFactory
    ) {
        $this->attributeOptionHandler = $attributeOptionHandler;
        $this->sellerAttributeRetriever = $sellerAttributeRetriever;
        $this->storeManager = $storeManager;
        $this->sellerFactory = $sellerFactory;
    }

    /**
     * @param int $sellerId
     * @param int | string | null $storeId
     * @return MarketplacerSellerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getById($sellerId, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $sellerAttribute = $this->sellerAttributeRetriever->getAttribute();
        $option = $this->attributeOptionHandler->getAttributeOptionById($sellerAttribute, $sellerId);
        if (!$option) {
            throw new NoSuchEntityException(__('The seller option with ID "%1" does not exist.', $sellerId ?? ''));
        }

        $sellerName = $option->getLabel();
        if ($option->getStoreLabels()) {
            foreach ($option->getStoreLabels() as $label) {
                if ($label->getLabel() && $label->getStoreId() == $storeId) {
                    $sellerName = $label->getLabel();
                    break;
                }
            }
        }

        $seller = $this->sellerFactory->create();
        $seller->setSellerId($sellerId);
        $seller->setName($sellerName);

        return $seller;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        throw new LocalizedException(
            __('This method is not implemented.'
                . ' To Use it please install Marketplacer_Seller module or ask your developers to implement your own solution.'
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function save($seller)
    {
        throw new LocalizedException(
            __('This method is not implemented.'
                . ' To Use it please install Marketplacer_Seller module or ask your developers to implement your own solution.'
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteById($sellerId)
    {
        throw new LocalizedException(
            __('This method is not implemented.'
                . ' To Use it please install Marketplacer_Seller module or ask your developers to implement your own solution.'
            )
        );
    }
}
