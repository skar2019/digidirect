<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductAttachments
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Controller\Adminhtml\Conditions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magezon\Core\Block\Adminhtml\Conditions\Product;

class ProductList extends Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Product
     */
    protected $gridProduct;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     * @param Registry $registry
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory,
        Registry $registry,
        Product $gridProduct,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_coreRegistry = $registry;
        $this->gridProduct = $gridProduct;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * Grid Action
     * Display list of products related to current post
     *
     * @return Raw
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if (isset($data['rule'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
        }
        unset($data['conditions_serialized']);
        unset($data['actions_serialized']);
        $file = $this->ruleFactory->create();
        $file->loadPost($data);
        $this->_coreRegistry->unregister('mgz_conditions_model');
        $this->_coreRegistry->register('mgz_conditions_model', $file);
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Magezon\Core\Block\Adminhtml\Conditions\Product::class,
                'product.grid'
            )->toHtml()
        );
    }
}
