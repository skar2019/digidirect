<?php
namespace Ewave\Faq\Model\Export\Excel;

/**
 * Class MetadataProvider
 *
 * @package Ewave\Faq\Model\Export\Excel
 */
class MetadataProvider extends \Magento\Framework\Convert\Excel
{
    /**
     * Get a Single XML Row
     *
     * @param array $row
     * @param boolean $useCallback
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getXmlRow($row, $useCallback)
    {
        if ($useCallback && $this->_rowCallback) {
            $row = call_user_func($this->_rowCallback, $row);
        }
        $xmlData = [];
        $xmlData[] = '<Row>';

        foreach ($row as $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $value = htmlspecialchars($value);
            $dataType = is_numeric($value) && $value[0] !== '+' && $value[0] !== '0' ? 'Number' : 'String';

            /**
             * Security enhancement for CSV data processing by Excel-like applications.
             *
             * @see https://bugzilla.mozilla.org/show_bug.cgi?id=1054702
             *
             * @var $value string|\Magento\Framework\Phrase
             */
            if (!is_string($value)) {
                $value = (string)$value;
            }
            if (isset($value[0]) && in_array($value[0], ['=', '+', '-'])) {
                $value = ' ' . $value;
            }

            $value = str_replace("\r\n", '&#10;', $value);
            $value = str_replace("\r", '&#10;', $value);
            $value = str_replace("\n", '&#10;', $value);

            $xmlData[] = '<Cell><Data ss:Type="' . $dataType . '">' . $value . '</Data></Cell>';
        }
        $xmlData[] = '</Row>';

        return join('', $xmlData);
    }
}
