<?php
namespace Digidirect\Brands\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class ViewBrand extends Template
{
    protected $productCollectionFactory;
    protected $attributeRepository;
    protected $storeManager;

    public function __construct(
        Template\Context $context,
        ProductCollectionFactory $productCollectionFactory,
        AttributeRepositoryInterface $attributeRepository,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->attributeRepository = $attributeRepository;
        $this->storeManager = $storeManager;
    }

    public function getBrandName()
    {
        $brandSlug = $this->getRequest()->getParam('brand');
        return ucwords(str_replace('-', ' ', $brandSlug));
    }

    public function getProducts()
    {
        $brandSlug = $this->getRequest()->getParam('brand');
        $attributeCode = 'brand';

        $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);
        $options = $attribute->getOptions();

        $brandId = null;
        foreach ($options as $option) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $option->getLabel()));
            if ($slug === $brandSlug) {
                $brandId = (int) $option->getValue();
                break;
            }
        }

        if (!$brandId) return [];

        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'small_image', 'url_key'])
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', ['neq' => 1])
            ->addAttributeToFilter($attributeCode, $brandId);

        return $collection;
    }
}
