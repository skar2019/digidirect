<?php

namespace Digidirect\Navigation\Model\Frontend;

use Magento\Framework\DataObject;

/**
 * Class is used to format menu items fetched from db or cache
 * @since 1.3.0
 * @api
 */
class MenuSetDataFormatter
{
    /**
     * @param DataObject $dataObject
     * @return bool|string
     */
    public function toJson(DataObject $dataObject)
    {
        return $dataObject->toJson();
    }

    /**
     * @param DataObject $dataObject
     * @return array
     */
    public function toArray(DataObject $dataObject)
    {
        return $dataObject->toArray();
    }

    /**
     * This method is incorrect and should be avoided. Left for backward compatibility for all projects
     *
     * @param DataObject $dataObject
     * @return object
     */
    public function toObjectArray(DataObject $dataObject)
    {
        return json_decode($dataObject->toJson());
    }
}
