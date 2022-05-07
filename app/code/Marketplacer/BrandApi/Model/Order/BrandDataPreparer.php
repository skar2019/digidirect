<?php

namespace Marketplacer\BrandApi\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Marketplacer\BrandApi\Api\BrandRepositoryInterface;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;

class BrandDataPreparer
{
    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * @param BrandRepositoryInterface $brandRepository
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     */
    public function __construct(
        BrandRepositoryInterface $brandRepository,
        BrandAttributeRetrieverInterface $brandAttributeRetriever
    ) {
        $this->brandRepository = $brandRepository;
        $this->brandAttributeRetriever = $brandAttributeRetriever;
    }

    /**
     * @param string|null $brandId
     * @param int | string | null $storeId
     * @return string|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getBrandNameById($brandId, $storeId = null)
    {
        if (!$brandId) {
            return null;
        }

        try {
            $brand = $this->brandRepository->getById($brandId, $storeId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $brand->getName();
    }

    /**
     * @param AbstractItem $quoteItem
     * @return mixed
     * @throws LocalizedException
     */
    public function getBrandIdByQuoteItem(AbstractItem $quoteItem)
    {
        $brandAttributeCode = $this->brandAttributeRetriever->getAttributeCode();
        return $quoteItem->getProduct()->getData($brandAttributeCode);
    }

    /**
     * @param Quote $quote
     * @return array
     * @throws LocalizedException
     */
    public function getBrandIdsByQuote(Quote $quote)
    {
        $result = [];
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            $brandId = $this->getBrandIdByQuoteItem($quoteItem);
            if ($brandId) {
                $result[$brandId] = $brandId;
            }
        }
        return $result;
    }

    /**
     * @param Quote $quote
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getBrandNamesByQuoteItems(Quote $quote)
    {
        $result = [];
        foreach ($this->getBrandIdsByQuote($quote) as $brandId) {
            $brandName = $this->getBrandNameById($brandId, $quote->getStoreId());
            if (!empty($brandName)) {
                $result[] = $brandName;
            }
        }
        return implode(', ', $result);
    }
}
