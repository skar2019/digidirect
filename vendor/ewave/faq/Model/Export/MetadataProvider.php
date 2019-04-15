<?php
namespace Ewave\Faq\Model\Export;

use Magento\Framework\Api\Search\DocumentInterface;

/**
 * Class MetadataProvider
 *
 * @package Ewave\Faq\Model\Export
 */
class MetadataProvider extends \Magento\Ui\Model\Export\MetadataProvider
{
    /**
     * Returns row data
     *
     * @param DocumentInterface $document
     * @param array $fields
     * @param array $options
     * @return array
     */
    public function getRowData(DocumentInterface $document, $fields, $options)
    {
        $row = [];
        foreach ($fields as $column) {
            if (isset($options[$column])) {
                $key = $document->getCustomAttribute($column)->getValue();
                if (isset($options[$column][$key])) {
                    $row[] = $options[$column][$key];
                } else {
                    $row[] = '';
                }
            } else {
                $value = $document->getCustomAttribute($column)->getValue();
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $row[] = $value;
            }
        }
        return $row;
    }
}
