<?php

namespace Ewave\Banner\Model\Attributes\TargetType;

use Ewave\Banner\Helper\IssetTrait;
use Magento\Banner\Model\Banner as BannerModel;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Product implements TargetTypeInterface
{
    use IssetTrait;

    const TARGET_TYPE_REQUEST_CODE = 'product_id';

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var array
     */
    protected $productUrlById = [];

    /**
     * Product constructor.
     *
     * @param ProductCollectionFactory $collectionFactory
     */
    public function __construct(
        ProductCollectionFactory $collectionFactory
    ) {
        $this->productCollectionFactory = $collectionFactory;
    }

    /**
     * @param BannerModel $banner
     * @return null
     */
    public function getBackendAttributeForSave(BannerModel $banner)
    {
        $array = explode('/', $banner->getData(self::TARGET_TYPE_REQUEST_CODE));
        return $this->getByKey($array, 1, null);
    }

    /**
     * @param int|string $productId
     * @return null|string
     */
    public function getUrl($productId)
    {
        $url = null;
        if ($productId) {
            $product = $this->getProductById($productId);
            $url = $product ? $product->getProductUrl() : null;
        }
        return $url;
    }

    /**
     * @param int $entityId
     * @return string|null
     */
    public function getValue($entityId)
    {
        $value = null;
        if ($entityId) {
            $product = $this->getProductById($entityId);
            $value = $product ? $product->getName() : null;
        }
        return $value;
    }

    /**
     * @param BannerModel $banner
     * @param null $currentAttribute
     * @return null|string
     */
    public function getBackendAttributeForEdit(BannerModel $banner, $currentAttribute = null)
    {
        $targetId = $banner->getData('target_id');
        $banner->setData(self::TARGET_TYPE_REQUEST_CODE, $targetId);
        return ($targetId && $currentAttribute && $currentAttribute == self::TARGET_TYPE_REQUEST_CODE) ?
            'product/' . $targetId
            : null;
    }

    /**
     * @param int $productId
     * @return ProductInterface|\Magento\Catalog\Model\Product
     */
    protected function getProductById($productId)
    {
        if (!isset($this->productUrlById[$productId])) {
            /**
             * @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection
             */
            $collection = $this->productCollectionFactory->create();

            /**
             * @var $product \Magento\Catalog\Model\Product
             */
            $product = $collection->addIdFilter($productId)
                ->addAttributeToSelect('name')
                ->setPageSize(1)
                ->addUrlRewrite()
                ->getFirstItem();
            $this->productUrlById[$productId] = $product;
        }
        return $this->productUrlById[$productId];
    }
}
