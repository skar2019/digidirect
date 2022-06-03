<?php

namespace Marketplacer\Brand\Helper;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Url as UrlHelper;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;

class Data extends AbstractHelper
{
    public const BULK_OPERATIONS_CHUNK_SIZE = 100;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * Data constructor.
     * @param Context $context
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     * @param BrandRepositoryInterface $brandRepository
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        Context $context,
        BrandAttributeRetrieverInterface $brandAttributeRetriever,
        BrandRepositoryInterface $brandRepository,
        UrlHelper $urlHelper
    ) {
        parent::__construct($context);
        $this->brandAttributeRetriever = $brandAttributeRetriever;
        $this->brandRepository = $brandRepository;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param Product $product
     * @return BrandInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getBrandByProduct(Product $product)
    {
        $brandId = $product->getData($this->brandAttributeRetriever->getAttributeCode());
        if (!$brandId) {
            return null;
        }

        try {
            $brand = $this->brandRepository->getById($brandId, $product->getStoreId());
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $brand;
    }

    /**
     * @param BrandInterface $brand
     * @return bool|string|null
     */
    public function getBrandUrl(BrandInterface $brand)
    {
        try {
            $url = $this->urlHelper->getBrandUrlById($brand->getBrandId(), $brand->getStoreId());
        } catch (Exception $e) {
            return null;
        }
        return $url;
    }
}
