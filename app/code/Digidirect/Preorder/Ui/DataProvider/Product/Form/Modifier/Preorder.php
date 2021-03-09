<?php

namespace Digidirect\PreOrder\Ui\DataProvider\Product\Form\Modifier;

use Digidirect\PreOrder\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Class Preorder
 *
 * @package Digidirect\PreOrder\Ui\DataProvider\Product\Form\Modifier
 */
class Preorder extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @param LocatorInterface $locator
     */
    public function __construct(
        LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $model = $this->locator->getProduct();
        $modelId = $model->getId();
        $data[$modelId][self::DATA_SOURCE_DEFAULT]
        [ProductAttributeInterface::CODE_PREORDER_NOTE] = $model->getDigidirectPreorderNote();
        $data[$modelId][self::DATA_SOURCE_DEFAULT]
        [ProductAttributeInterface::CODE_PREORDER_CART_LABEL] = $model->getDigidirectPreorderCartLabel();
        $data[$modelId][self::DATA_SOURCE_DEFAULT][ProductAttributeInterface::CODE_PRODUCT_AVAILABILITY_DATE]
            = $model->getDigidirectProductAvailabilityDate();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
