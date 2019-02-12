<?php

namespace Ewave\CheckoutFields\Model\Component\Type;

/**
 * Class Multiselect
 * @package Ewave\CheckoutFields\Model\Component\Type
 */
class Multiselect extends AbstractType
{
    /**
     * @return string
     */
    protected function getComponent()
    {
        return 'Magento_Ui/js/form/element/multiselect';
    }

    /**
     * @return string
     */
    protected function getElementTemplate()
    {
        return 'ui/form/element/multiselect';
    }
}
