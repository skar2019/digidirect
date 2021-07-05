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
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\ProductLabels\Block\Adminhtml\Rule\Edit\Grid;
use Mageplaza\ProductLabels\Controller\Adminhtml\Rule;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Model\Indexer\RuleIndexer;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class Products
 * @package Mageplaza\ProductLabels\Controller\Adminhtml\Grid
 */
class Products extends Rule
{
    /**
     * JS helper
     *
     * @var Js
     */
    protected $_jsonHelper;

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * Products constructor.
     *
     * @param Context $context
     * @param RuleFactory $ruleFactory
     * @param Registry $coreRegistry
     * @param HelperData $helperData
     * @param RuleIndexer $ruleIndexer
     * @param PageFactory $pageFactory
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        RuleFactory $ruleFactory,
        Registry $coreRegistry,
        HelperData $helperData,
        RuleIndexer $ruleIndexer,
        PageFactory $pageFactory,
        JsonHelper $jsonHelper
    ) {
        $this->_jsonHelper  = $jsonHelper;
        $this->_pageFactory = $pageFactory;

        parent::__construct($context, $ruleFactory, $coreRegistry, $helperData, $ruleIndexer);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $page = $this->_pageFactory->create();
        $html = $page->getLayout()
            ->createBlock(Grid::class)->toHtml();
        if ($this->getRequest()->getParam('loadGrid')) {
            $html = $this->_jsonHelper->jsonEncode($html);
        }

        return $this->getResponse()->representJson($html);
    }
}
