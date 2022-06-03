<?php

namespace Marketplacer\Brand\Model\ResourceModel\Brand\Grid;

use Marketplacer\Base\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idEntityKey = 'brand_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
            'Marketplacer\Brand\Model\ResourceModel\Brand'
        );
    }
}
