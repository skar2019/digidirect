<?php

namespace Ewave\Migration\Helper;

use Magento\Framework\DataObject;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class Data
{
    /**
     * Setup model
     *
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * Data constructor.
     * @param ModuleDataSetupInterface $setup
     */
    public function __construct(
        ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    /**
     * @param array $item
     * @return string
     */
    public function prepareSku(DataObject $item): string
    {
        //if (!empty($item)) {}
        $sku = 'kit_' . $item->getData('kit_option') . '_'
            . substr($item->getData('Name'), 0, 50) . '_' .$item->getData('ID');
        return $sku;
    }

    /**
     * @param string|null $imagesString
     * @return array
     */
    public function getImagesList(?string $imagesString): array
    {
        $images = [];
        if (!empty($imagesString)) {
            $images = explode(',', $imagesString);
            foreach ($images as $k => $image) {
                $images[$k] = explode(':', $image);
            }
        }
        return $images;
    }

    /**
     * @param array $images
     * @return int
     */
    public function getMainImageIndex(array $images): int
    {
        if (!empty($images)) {
            foreach ($images as $k => $image) {
                if (!empty($image[1])) {
                    return $k;
                }
            }
        }

        return 0;
    }

    /**
     * @param string $input
     * @return string
     */
    public function htmlEntityDecode(?string $input): string
    {
        return html_entity_decode($input);
    }

    /**
     * @param string $attributeCode
     * @param string $label
     * @param ProductInterface $product
     * @return int|null
     */
    public function getAttributeOptionId(string $attributeCode, string $label, ProductInterface $product): ?int
    {
        $attr = $product->getResource()->getAttribute($attributeCode);
        if ($attr->usesSource()) {
            $option_id = $attr->getSource()->getOptionId($label);
        }

        return $option_id ?? null;
    }

    /**
     * @param string $attributeCode
     * @param string $label
     * @param ProductInterface $product
     * @return int|null
     */
    public function addAttributeOptionId(string $attributeCode, string $label, ProductInterface $product): ?int
    {
        $optionTable = $this->setup->getTable('eav_attribute_option');
        $optionValueTable = $this->setup->getTable('eav_attribute_option_value');

        $attr = $product->getResource()->getAttribute($attributeCode);

        $data = [
            'attribute_id' => $attr->getData('attribute_id'),
        ];
        $this->setup->getConnection()->insert($optionTable, $data);
        $intOptionId = $this->setup->getConnection()->lastInsertId($optionTable);

        $data = ['option_id' => $intOptionId, 'store_id' => 0, 'value' => $label];
        $this->setup->getConnection()->insert($optionValueTable, $data);

        return $intOptionId ?? null;
    }
}
