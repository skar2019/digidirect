<?php

namespace Marketplacer\Brand\Model\Brand;

use Magento\Framework\Exception\LocalizedException;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Model\Brand;

class Validator
{
    /**
     * @var array
     */
    protected $requiredFields = [
        BrandInterface::NAME => 'name',
    ];

    /**
     * @param BrandInterface $brand
     * @param bool $onlyExistingData
     * @return void
     * @throws LocalizedException
     */
    public function validate(BrandInterface $brand, $onlyExistingData = false)
    {
        if ($brand->getData('_skip_validation_flag')) {
            return;
        }

        /**
         * @var $brand Brand
         */
        if (!$onlyExistingData) {
            foreach ($this->requiredFields as $dataKey => $fieldName) {
                if (!$brand->hasData($dataKey)) {
                    throw new LocalizedException(__('%1 is required', $fieldName));
                }
            }
        }
        foreach ($this->requiredFields as $dataKey => $fieldName) {
            if ($brand->hasData($dataKey) && empty($brand->getData($dataKey))) {
                throw new LocalizedException(__('%1 is required and can\'t be empty', $fieldName));
            }
        }
    }
}
