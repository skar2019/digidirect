<?php

namespace Marketplacer\Brand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;
use Marketplacer\Base\Api\CacheInvalidatorInterface;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Helper\Config;

/**
 * Class AbstractBrandEditAction
 * @package Marketplacer\Brand\Controller\Adminhtml\Brand
 */
abstract class AbstractBrandAction extends Action
{
    const ADMIN_RESOURCE = 'Marketplacer_Brand::brand';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var CacheInvalidator
     */
    protected $cacheInvalidator;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BrandRepositoryInterface $brandRepository
     * @param Config $configHelper
     * @param CacheInvalidatorInterface $cacheInvalidator
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BrandRepositoryInterface $brandRepository,
        Config $configHelper,
        CacheInvalidatorInterface $cacheInvalidator
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->brandRepository = $brandRepository;
        $this->configHelper = $configHelper;
        $this->cacheInvalidator = $cacheInvalidator;
    }

    /**
     * @return bool
     */
    protected function isAdminEditAllowed()
    {
        if (!$this->configHelper->isAdminEditAllowed()) {
            return false;
        }

        return true;
    }

    /**
     * @return Redirect
     */
    protected function processEditNotAllowedRedirect()
    {
        $this->messageManager->addErrorMessage(__('Brand editing by admin is not allowed in configuration'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}
