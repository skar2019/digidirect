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
 * @package     Mageplaza_Search
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\ProductLabels\Helper\Data;
use Mageplaza\ProductLabels\Controller\Adminhtml\Rule\ApplyRules;

/**
 * Class ProductLabelUpdate
 * @package Mageplaza\ProductLabels\Observer
 */
class ProductLabelUpdate implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Data
     */
    protected $applyRules;

    /**
     * ProductLabelUpdate constructor.
     * @param Data $data
     * @param ApplyRules $applyRules
     */
    public function __construct(
        Data $data,
        ApplyRules $applyRules
    )
    {
        $this->helperData = $data;
        $this->applyRules = $applyRules;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled()) {
            $this->applyRules->execute();
        }
        return $this;
    }
}
