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

namespace Mageplaza\ProductLabels\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Model\Indexer\RuleIndexer;
use Mageplaza\ProductLabels\Model\RuleFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Rule
 * @package Mageplaza\ProductLabels\Controller\Adminhtml
 */
abstract class Rule extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_ProductLabels::rule';

    /**
     * Rule model factory
     *
     * @var RuleFactory
     */
    public $ruleFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var RuleIndexer
     */
    protected $ruleIndexer;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param RuleFactory $ruleFactory
     * @param Registry $coreRegistry
     * @param HelperData $helperData
     * @param RuleIndexer $ruleIndexer
     */
    public function __construct(
        Context $context,
        RuleFactory $ruleFactory,
        Registry $coreRegistry,
        HelperData $helperData,
        RuleIndexer $ruleIndexer
    ) {
        $this->ruleFactory  = $ruleFactory;
        $this->coreRegistry = $coreRegistry;
        $this->helperData   = $helperData;
        $this->ruleIndexer  = $ruleIndexer;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Mageplaza\ProductLabels\Model\Rule
     */
    protected function initRule($register = false)
    {
        $ruleId = (int) $this->getRequest()->getParam('id');

        /** @var \Mageplaza\ProductLabels\Model\Rule $rule */
        $rule = $this->ruleFactory->create();

        if ($ruleId) {
            $rule->load($ruleId);
            if (!$rule->getId()) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));

                return false;
            }
        }
        if ($register) {
            $this->coreRegistry->register('mageplaza_productlabels_rule', $rule);
        }

        return $rule;
    }
}
