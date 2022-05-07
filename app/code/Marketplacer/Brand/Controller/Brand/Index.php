<?php

namespace Marketplacer\Brand\Controller\Brand;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Marketplacer\Brand\Helper\Config as ConfigHelper;

/**
 * Class Index
 * @package Marketplacer\Brand\Controller\Brand
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ConfigHelper $configHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if (!$this->configHelper->isEnabledOnStorefront()) {
            $this->_redirect('noroute');
        }

        return $this->resultPageFactory->create();
    }
}
