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
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Controller\Adminhtml\Ajax;

use Magento\Framework\Controller\Result\JsonFactory;

class LibraryTemplate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magezon\Builder\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context    
     * @param \Magezon\Builder\Helper\Data        $dataHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magezon\Builder\Helper\Data $dataHelper,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $result = [];
        $post   = $this->getRequest()->getPostValue();
        if (isset($post['url']) && $post['url']) {
            $result = $this->dataHelper->getTemplates($post['url']);
        }
    	$resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}