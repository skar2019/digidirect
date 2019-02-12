<?php
namespace Ewave\ProductPriority\Model\Config\Source;

/**
 * Class SortBy
 * @package Ewave\Productpriority\Model\Config\Source
 */
class Sort implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_sortTypes;

    /**
     * Sort constructor.
     * @param array $sortTypes
     */
    public function __construct($sortTypes = [])
    {
        $this->_sortTypes = $sortTypes;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_sortTypes as $type => $label) {
            $options[] = ['value' => $type, 'label' => $label];
        }
        return $options;
    }
}
