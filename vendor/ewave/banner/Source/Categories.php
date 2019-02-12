<?php

namespace Ewave\Banner\Source;

use Ewave\Banner\Model\Attributes\CategoryTree;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CategoryTree
     */
    protected $_categoryTree;

    /**
     * Categories constructor.
     * @param CategoryTree $categoryCollection
     */
    public function __construct(CategoryTree $categoryCollection)
    {
        $this->_categoryTree = $categoryCollection;
    }

    /**
     * Get options
     *
     * @return []
     */
    public function toOptionArray()
    {
        $collection = $this->_categoryTree->loadByIds([]);
        $result = [['value' => 0,'label' => 'Please select a category']];
        foreach ($collection->getNodes() as $item) {
            $result[(int)$item->getId()] = [
                'value' => (int)$item->getId(),
                'title' => $item->getName(),
                'label' => $this->_getDashes($item->getLevel() - 1) . $item->getName(),
            ];
        }
        return $result;
    }

    /**
     * Get dashes
     *
     * @param int $count
     * @return string
     */
    protected function _getDashes($count)
    {
        $result = '';
        for ($i = 0; $i < $count; $i++) {
            $result .= '-- ';
        }
        return $result;
    }
}
