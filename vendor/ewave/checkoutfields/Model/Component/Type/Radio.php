<?php

namespace Ewave\CheckoutFields\Model\Component\Type;

/**
 * Class Radio
 * @package Ewave\CheckoutFields\Model\Component\Type
 */
class Radio extends AbstractType
{
    /**
     * @return string
     */
    protected function getComponent()
    {
        return 'Magento_Ui/js/form/element/checkbox-set';
    }

    /**
     * @return string
     */
    protected function getElementTemplate()
    {
        return 'ui/form/element/checkbox-set';
    }
}
