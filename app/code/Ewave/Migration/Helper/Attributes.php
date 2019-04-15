<?php

namespace Ewave\Migration\Helper;

/**
 * Class Attributes
 * @package Ewave\Migration\Helper
 */
class Attributes
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Attributes constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    protected $mappingData = [
        'frontend_input' => [
            'Dropdown' => 'select',
            'Text Field' => 'text',
            'Text Area' => 'textarea',
            'Text Editor' => 'texteditor',
            'Date' => 'date',
            'Multiple Select' => 'multiselect',
            'Yes/No' => 'boolean'
        ],
        'is_required' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_unique' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_global' => [
            'Store View' => 0,
            'Website' => 2,
            'Global' => 1
        ],
        'frontend_class' => [
            'None' => '',
            'Decimal Number' => 'validate-number',
            'Integer Number' => 'validate-digits',
            'Email' => 'validate-email',
            'URL' => 'validate-url',
            'Letters' => 'validate-alpha',
            'Letters (a-z, A-Z) or Numbers (0-9)' => 'validate-alphanum'
        ],
        'is_visible' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_searchable' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_filterable' => [
            'Filterable (with results)' => 1,
            'Filterable (no results)' => 2,
            'No' => 0
        ],
        'is_comparable' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_wysiwyg_enabled' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_used_for_promo_rules' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_used_for_price_rules' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_html_allowed_on_front' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_visible_on_front' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_visible_in_grid' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_filterable_in_grid' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_used_in_grid' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_visible_in_advanced_search' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_filterable_in_search' => [
            'Yes' => 1,
            'No' => 0
        ],
        'used_in_product_listing' => [
            'Yes' => 1,
            'No' => 0
        ],
        'used_for_sort_by' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_required_in_admin_store' => [
            'Yes' => 1,
            'No' => 0
        ]

    ];

    /**
     * @param array $row
     * @return array
     */
    public function prepareRowValues(array $row):array
    {
        foreach ($row as $column=>$value) {
            $row[$column] = $this->mappingData[$column][$value] ?? $row[$column];
        }

        if (empty($row['position'])) {
            $row['position'] = 100;
        }

        $row = array_filter($row, 'strlen');
        $this->prepareFrontLabel($row);
        $row['attribute_code'] = $this->prepareAttrCode($row['attribute_code']);

        if (!empty($row['options'])) {
            $row['options'] = $this->prepareOptions($row['options']);
        }

        return $row;
    }

    /**
     * @param array $row
     */
    protected function prepareFrontLabel(array &$row)
    {
        $stores = $this->storeManager->getStores();

        $row['frontend_label'] = [$row['frontend_label_0'] ?? $row['attribute_code']];
        unset($row['frontend_label_0']);

        foreach ($stores as $store) {
            $key = 'frontend_label_' . $store->getId();
            if (!empty($row[$key])) {
                $row['frontend_label'][] = $row[$key];
                unset($row[$key]);
            }
        }
    }

    /**
     * @param string|null $options
     * @return array
     */
    protected function prepareOptions(?string $options):array
    {
        $values = array_unique(array_map('trim', explode(',', $options)));
        $options = [];

        foreach ($values as $value) {
            $options[] = [
                'label' => $value,
            ];
        }

        return $options;
    }

    /**
     * @param string $code
     * @return string
     */
    public function prepareAttrCode(string $code):string
    {
        return substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                strtolower($code)
            ),
            0,
            255
        );
    }

    public function processDuplicateOptions(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute, array &$row)
    {
        if (!empty($row['options'])) {
            $options = $attribute->getOptions();

            foreach ($options as $option) {
                foreach ($row['options'] as $key=>$item) {
                    if (strtolower($item['label']) == strtolower($option->getLabel())) {
                        unset($row['options'][$key]);
                    }
                }
            }
        }
    }
}
