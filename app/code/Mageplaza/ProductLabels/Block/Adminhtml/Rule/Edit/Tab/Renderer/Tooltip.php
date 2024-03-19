<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Block\Adminhtml\Rule\Edit\Tab\Renderer;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ProductLabels\Helper\Data as HelperData;

/**
 * Class Tooltip
 * @package Mageplaza\ProductLabels\Block\Adminhtml\Rule\Edit\Tab\Renderer
 */
class Tooltip extends Extended
{
    protected $_template = 'Mageplaza_ProductLabels::rule/tooltip.phtml';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Tooltip constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        StoreManagerInterface $storeManager,
        Registry $registry,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->registry     = $registry;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return StoreInterface[]
     */
    public function getStores()
    {
        $stores = $this->storeManager->getStores(true);
        if (is_array($stores)) {
            usort($stores, function ($storeA, $storeB) {
                if ($storeA->getSortOrder() == $storeB->getSortOrder()) {
                    return $storeA->getId() < $storeB->getId() ? -1 : 1;
                }

                return ($storeA->getSortOrder() < $storeB->getSortOrder()) ? -1 : 1;
            });
        }

        return $stores;
    }

    /**
     * @param string $elementId
     *
     * @return mixed
     */
    public function getTooltipData($elementId)
    {
        $rule = $this->registry->registry('mageplaza_productlabels_rule');

        return HelperData::jsonDecode($rule->getData($elementId));
    }
}
