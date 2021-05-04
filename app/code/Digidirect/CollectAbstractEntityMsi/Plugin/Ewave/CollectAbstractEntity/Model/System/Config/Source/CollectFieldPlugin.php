<?php
namespace Digidirect\CollectAbstractEntityMSI\Plugin\Digidirect\CollectAbstractEntity\Model\System\Config\Source;

use Digidirect\CollectAbstractEntity\Model\System\Config\Source\CollectField;
use Digidirect\CollectAbstractEntityMSI\Api\Data\CollectFields\Constants;

/**
 * Class CollectFieldPlugin
 * @package Digidirect\CollectAbstractEntityMSI\Plugin\Digidirect\CollectAbstractEntity\Model\System\Config\Source
 */
class CollectFieldPlugin
{
    /**
     * @param CollectField $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToOptionArray(CollectField $subject, $result)
    {
        $result[] = [
            'value' => Constants::COLLECT_FIELD_INVENTORY_SOURCE,
            'label' => __('MSI Inventory Source')
        ];
        return $result;
    }
}
