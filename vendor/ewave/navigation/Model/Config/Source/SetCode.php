<?php

namespace Ewave\Navigation\Model\Config\Source;

use Ewave\Navigation\Api\Data\SetInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Returns options with set code as value
 * @since 1.3.0
 */
class SetCode extends Set implements ArrayInterface
{
    /**
     * Get all menu items sets using sets API
     *
     * @return []
     */
    public function toOptionArray()
    {
        $searchResults = $this->setRepository->getList($this->searchCriteriaBuilder->create());
        $items = $searchResults->getItems();
        $optionsArray = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                $optionsArray[] = [
                    'value' => $item[SetInterface::SET_CODE] ?? null,
                    'label' => $item[SetInterface::NAME] ?? null
                ];
            }
        }
        return $optionsArray;
    }
}
