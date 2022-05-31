<?php

namespace Marketplacer\Brand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Api\CacheInvalidatorInterface;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Api\Data\BrandInterfaceFactory;
use Marketplacer\Brand\Helper\Config;
use Marketplacer\Brand\Model\Brand;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Class Save
 * @package Marketplacer\Brand\Controller\Adminhtml\Brand
 */
class Save extends AbstractBrandAction
{
    /**
     * @var BrandInterfaceFactory
     */
    protected $brandFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Save constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BrandInterfaceFactory $brandFactory
     * @param BrandRepositoryInterface $brandRepository
     * @param StoreManagerInterface $storeManager
     * @param Config $configHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BrandInterfaceFactory $brandFactory,
        BrandRepositoryInterface $brandRepository,
        StoreManagerInterface $storeManager,
        Config $configHelper,
        CacheInvalidatorInterface $cacheInvalidator,
        LoggerInterface $logger
    ) {
        parent::__construct($context, $resultPageFactory, $brandRepository, $configHelper, $cacheInvalidator);
        $this->storeManager = $storeManager;
        $this->brandFactory = $brandFactory;
        $this->logger = $logger;
    }

    /**
     * Save action
     * @return ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        /**
         * @var $brand Brand
         */
        if (!$this->isAdminEditAllowed()) {
            return $this->processEditNotAllowedRedirect();
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $hasError = false;

            $brandId = $data[BrandInterface::BRAND_ID] ?? null;
            if (empty($data[BrandInterface::STORE_ID])) {
                $data[BrandInterface::STORE_ID] = Store::DEFAULT_STORE_ID;
            }
            $storeId = $data[BrandInterface::STORE_ID];

            unset($data['row_id'], $data['url_key']);

            if ($brandId) {
                $brand = $this->brandRepository->getById($brandId, $storeId);
            } else {
                $brand = $this->brandFactory->create();
            }
            $brand->addData($data);

            try {
                $this->brandRepository->save($brand);
                $this->cacheInvalidator->invalidate();
                $this->messageManager->addSuccessMessage(__('You saved Brand'));
            } catch (LocalizedException $e) {
                $hasError = true;
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Throwable $e) {
                $hasError = true;
                $this->logger->critical($e->getMessage());
                $this->messageManager->addErrorMessage('An error error occurred by saving the brand.');
            }

            $brandId = $brand->getBrandId();
            if (($hasError || $this->getRequest()->getParam('back')) && $brandId) {
                $params = [
                    'brand_id' => $brandId,
                    '_current' => true,
                    'store'    => $brand->getStoreId(),
                ];
                return $this->resultRedirectFactory->create()->setPath('*/*/edit', $params);
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
