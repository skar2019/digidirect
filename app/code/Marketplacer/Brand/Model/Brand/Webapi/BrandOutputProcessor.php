<?php

namespace Marketplacer\Brand\Model\Brand\Webapi;

use Marketplacer\Brand\Api\Data\BrandInterface;

class BrandOutputProcessor
{
    protected const ALLOWED_API_FIELDS = [
        'id',
        'name',
        'logo',
    ];

    /**
     * Filter result output array
     *
     * @param BrandInterface $brand
     * @param array $result
     * @return array
     */
    public function execute(
        BrandInterface $brand,
        array $result
    ): array {
        $filteredResult = [];
        foreach (self::ALLOWED_API_FIELDS as $allowedFieldName) {
            if (array_key_exists($allowedFieldName, $result)) {
                $filteredResult[$allowedFieldName] = $result[$allowedFieldName];
            } else {
                $filteredResult[$allowedFieldName] = '';
            }
        }

        $filteredResult['id'] = $brand->getBrandId();

        return $filteredResult;
    }
}
