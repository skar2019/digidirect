<?php

namespace Marketplacer\Brand\Model\Brand;

use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Model\Brand;

class BrandDataToOptionSetter
{
    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    protected $attributeOptionLabelFactory;

    /**
     * @param AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory
     */
    public function __construct(AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory)
    {
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
    }

    /**
     * @param BrandInterface $brand
     * @param AttributeOptionInterface $option
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setFromBrand(BrandInterface $brand, AttributeOptionInterface $option)
    {
        /**
         * @var $brand Brand
         */
        if ($brand->hasData(BrandInterface::NAME)) {
            $brandName = $brand->getName();
            if (empty($brandName)) {
                throw new LocalizedException(__('Brand Name is required'));
            }
            $this->setOptionLabelsFromBrand($option, $brand);
        }

        if (!$option->hasData(AttributeOptionInterface::IS_DEFAULT)) {
            $option->setIsDefault(0);
        }

        if (!$option->hasData(AttributeOptionInterface::SORT_ORDER)) {
            $option->setSortOrder(0);
        }
    }

    /**
     * @param AttributeOptionInterface $option
     * @param BrandInterface $brand
     * @return AttributeOptionInterface
     */
    protected function setOptionLabelsFromBrand(AttributeOptionInterface $option, BrandInterface $brand)
    {
        $brandName = $brand->getName();
        $storeId = $brand->getStoreId();
        if ($storeId == Store::DEFAULT_STORE_ID) {
            $option->setLabel($brandName);
        } else {
            $storeLabels = $option->getStoreLabels();

            $storeLabelExist = false;
            if ($storeLabels) {
                foreach ($storeLabels as $storeLabel) {
                    if ($storeLabel->getStoreId() === $storeId) {
                        $storeLabel->setLabel($brandName);
                        $storeLabelExist = true;
                        break;
                    }
                }
            }
            if (!$storeLabelExist) {
                $storeLabel = $this->attributeOptionLabelFactory->create();
                $storeLabel->setData([
                    AttributeOptionLabelInterface::LABEL    => $brandName,
                    AttributeOptionLabelInterface::STORE_ID => $storeId,
                ]);
                $storeLabels[] = $storeLabel;
            }

            $option->setStoreLabels($storeLabels);
        }

        return $option;
    }
}
