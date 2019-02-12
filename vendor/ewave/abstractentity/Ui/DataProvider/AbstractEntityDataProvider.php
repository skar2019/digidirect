<?php
namespace Ewave\AbstractEntity\Ui\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class AbstractEntityDataProvider extends DataProvider
{
    /**
     * {@inheritdoc}
     */
    public function addField($field, $alias = null)
    {
        return $this;
    }
}
