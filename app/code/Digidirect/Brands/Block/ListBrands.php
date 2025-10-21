<?php
namespace Digidirect\Brands\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class ListBrands extends Template
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

    public function getBrands()
    {
        $attributeCode = 'brand';
        $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);
        $options = $attribute->getOptions();

        $activeBrandIds = $this->getActiveBrandOptionIds($attributeCode);

        $brands = [];
        foreach ($options as $option) {
            $optionId = (int) $option->getValue();
            $label = trim($option->getLabel());
            if (!$optionId || !$label) continue;
            if (!in_array($optionId, $activeBrandIds)) continue;

            $firstLetter = strtoupper($label[0]);
            if (!isset($brands[$firstLetter])) $brands[$firstLetter] = [];

            $brands[$firstLetter][] = [
                'label' => $label,
                'url' => $this->getUrl('brands/view', ['brand' => strtolower(preg_replace('/[^a-z0-9]+/', '-', $label))])
            ];
        }

        ksort($brands);
        return $brands;
    }

    protected function getActiveBrandOptionIds($attributeCode)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect($attributeCode)
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', ['neq' => 1])
            ->addAttributeToFilter($attributeCode, ['notnull' => true])
            ->distinct(true);

        $brandIds = [];
        foreach ($collection as $product) {
            $value = $product->getData($attributeCode);
            if ($value) $brandIds[] = (int) $value;
        }

        return array_unique($brandIds);
    }
}
