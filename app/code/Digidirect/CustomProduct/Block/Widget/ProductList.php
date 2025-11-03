<?php
namespace Digidirect\CustomProduct\Block\Widget;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ProductList extends Template implements BlockInterface
{
    protected $_template = "widget/grid.phtml";
    protected $productCollectionFactory;
    protected $imageHelper;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    public function getProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'small_image', 'sku'])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', ['in' => [2, 3, 4]]);

        $skusParam = $this->getData('skus');
        if ($skusParam) {
            $skus = array_filter(array_map('trim', explode(',', $skusParam)));
            if (!empty($skus)) {
                $collection->addAttributeToFilter('sku', ['in' => $skus]);
            }
        }

        $categoryId = $this->getData('category_id');
        if ($categoryId) {
            $collection->joinField(
                'category_id',
                'catalog_category_product',
                'category_id',
                'product_id=entity_id',
                null,
                'left'
            )->addAttributeToFilter('category_id', ['eq' => (int)$categoryId]);
        }

        $minPrice = $this->getData('min_price');
        $maxPrice = $this->getData('max_price');
        if ($minPrice !== null && $minPrice !== '') {
            $collection->addAttributeToFilter('price', ['gteq' => (float)$minPrice]);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $collection->addAttributeToFilter('price', ['lteq' => (float)$maxPrice]);
        }

        $limit = (int)$this->getData('limit') ?: 8;
        $collection->setPageSize($limit);

        return $collection;
    }

    public function getImageUrl($product)
    {
        try {
            return $this->imageHelper->init($product, 'product_small_image')->getUrl();
        } catch (\Exception $e) {
            return '';
        }
    }
}
