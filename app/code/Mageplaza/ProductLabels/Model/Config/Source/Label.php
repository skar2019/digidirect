<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Labels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Model\Config\Source;

use Mageplaza\ProductLabels\Model\ResourceModel\Rule\CollectionFactory;

/**
 * Class Label
 * @package Mageplaza\ProductLabels\Model\Config\Source
 */
class Label implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Label constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => 0, 'label' => __('-- Please Select --')];
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('enabled', 1);
        foreach ($collection as $item) {
            $options[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $options;
    }
}
