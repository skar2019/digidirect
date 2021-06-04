<?php

namespace Ewave\CheckoutFields\Model\Component\Type;

/**
 * Class Select
 * @package Ewave\CheckoutFields\Model\Component\Type
 */
class Select extends AbstractType
{
    /**
     * @return string
     */
    protected function getComponent()
    {
        return 'Magento_Ui/js/form/element/select';
    }

    /**
     * @return string
     */
    protected function getElementTemplate()
    {
        return 'ui/form/element/select';
    }
}
