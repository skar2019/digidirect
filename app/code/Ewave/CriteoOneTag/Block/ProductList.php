<?php

namespace Ewave\CriteoOneTag\Block;

use Magento\Catalog\Model\Category;

/**
 * Class ProductList
 *
 * @package Ewave\CriteoOneTag\Block
 */
class ProductList extends AbstractTag
{
    const COUNT_LISTING_PRODUCTS = 3;
    const CATALOG_PRODUCT_RELATED = 'catalog.product.related';
    const CHECKOUT_CART_CROSSSELL = 'checkout.cart.crosssell';

    /**
     * Catalog Product collection
     * @var \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection
     */
    protected $productCollection;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * ProductList constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\Serialize\Serializer\Json     $serializer
     * @param \Magento\Catalog\Model\Layer\Resolver            $layerResolver
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        array $data = []
    ) {
        $this->catalogLayer = $layerResolver->get();
        parent::__construct($context, $registry, $serializer, $data);
    }

    /**
     * Retrieves a current category
     *
     * @return Category
     */
    public function getCurrentCategory()
    {
        /** @var Category $category */
        $category = null;
        if ($this->catalogLayer) {
            $category = $this->catalogLayer->getCurrentCategory();
        } elseif ($this->registry->registry('current_category')) {
            $category = $this->registry->registry('current_category');
        }
        return $category;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection | null
     */
    protected function _getProducts()
    {
        /** @var Category $category */
        $category = $this->getCurrentCategory();

        if ($category
            && (
                $category->getDisplayMode() === null
                || in_array($category->getDisplayMode(), [Category::DM_MIXED, Category::DM_PRODUCT], true)
            )
        ) {
            return $this->_getProductCollection();
        }
        return null;
    }

    /**
     * Returns an instance of an assigned block via a layout update file
     *
     * @return mixed
     */
    public function getListBlock()
    {
        return $this->getLayout()->getBlock($this->getData('block_name'));
    }

    /**
     * Retrieve loaded category collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection | null
     */
    protected function _getProductCollection()
    {

        if ($this->productCollection === null) {
            $this->productCollection = $this->getListBlock()->getLoadedProductCollection();
        }

        if ((null === $this->productCollection)
            && ($this->getData('block_name') == self::CATALOG_PRODUCT_RELATED
                || $this->getData('block_name') == self::CHECKOUT_CART_CROSSSELL)
        ) {
            $this->productCollection = $this->getListBlock()->getItems();
        }

        return $this->productCollection;
    }

    /**
     * @return string
     */
    public function getItem()
    {
        $productCollection = $this->_getProducts();
        $productSkus = [];
        $i = 0;
        if (!empty($productCollection)) {
            foreach ($productCollection as $product) {
                $productSkus[] = $product->getSku();
                if (++$i >= self::COUNT_LISTING_PRODUCTS) {
                    break;
                }
            }
        }
        return $this->getStringItem($this->getDataType(), json_encode($productSkus));
    }
}
