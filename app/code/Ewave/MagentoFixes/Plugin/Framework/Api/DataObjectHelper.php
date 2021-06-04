<?php


namespace Ewave\MagentoFixes\Plugin\Framework\Api;

/**
 * Class DataObjectHelper
 * @package Ewave\MagentoFixes\Plugin\Framework\Api
 */
class DataObjectHelper
{
    /**
     * @param \Magento\Framework\Api\DataObjectHelper $subject
     * @param $dataObject
     * @param array $data
     * @param $interfaceName
     * @return array
     */
    public function beforePopulateWithArray(
        \Magento\Framework\Api\DataObjectHelper $subject,
        $dataObject, array $data, $interfaceName
    )
    {
        if ($interfaceName == \Magento\Quote\Api\Data\TotalsInterface::class) {
            if ($dataObject instanceof \Magento\Quote\Model\Cart\Totals) {
                if (
                    isset($data['extension_attributes']) &&
                    ($data['extension_attributes'] instanceof \Magento\Quote\Api\Data\AddressExtension)
                ) {
                    unset($data['extension_attributes']);
                }
            }
        }
        return [$dataObject, $data, $interfaceName];
    }
}