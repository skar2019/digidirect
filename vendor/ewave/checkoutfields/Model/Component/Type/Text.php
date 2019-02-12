<?php

namespace Ewave\CheckoutFields\Model\Component\Type;

/**
 * Class Text
 * @package Ewave\CheckoutFields\Model\Component\Type
 */
class Text extends AbstractType
{
    /**
     * @return string
     */
    protected function getComponent()
    {
        return 'Magento_Ui/js/form/element/abstract';
    }

    /**
     * @return string
     */
    protected function getElementTemplate()
    {
        return 'ui/form/element/input';
    }
}
