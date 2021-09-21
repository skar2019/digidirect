<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Banner;

use Amasty\BannerSlider\Model\Banner;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_BannerSlider::banners_banner';

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Amasty\BannerSlider\Api\BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * @var \Amasty\BannerSlider\Model\BannerFactory
     */
    private $bannerFactory;

    public function __construct(
        Action\Context $context,
        \Amasty\BannerSlider\Api\BannerRepositoryInterface $bannerRepository,
        \Amasty\BannerSlider\Model\BannerFactory $bannerFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->dataPersistor = $dataPersistor;
        $this->bannerRepository = $bannerRepository;
        $this->bannerFactory = $bannerFactory;
    }

    public function execute()
    {
        $bannerId = (int)$this->getRequest()->getParam('id');
        if ($bannerId) {
            try {
                $storeId = (int)$this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);
                $model = $this->bannerRepository->getById($bannerId, $storeId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Banner no longer exists.'));

                return $this->getRedirect('*/*/index');
            }
        } else {
            /** @var Banner $model */
            $model = $this->bannerFactory->create();
        }

        // set entered data if was error when we do save
        $data = $this->dataPersistor->get(Banner::PERSIST_NAME);
        if (!empty($data) && !$model->getId()) {
            $model->addData($data);
        }
        $this->coreRegistry->register(Banner::PERSIST_NAME, $model);

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->updateTitles($resultPage, $model);

        return $resultPage;
    }

    private function getRedirect(string $path = '', array $params = []): Redirect
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($path) {
            $redirect->setPath($path, $params);
        } else {
            $redirect->setRefererUrl();
        }

        return $redirect;
    }

    private function updateTitles(Page $page, Banner $model): void
    {
        $title = $model->getId() ?
            __('Edit Banner # %1', $model->getId())
            : __('New Banner');
        $page->addBreadcrumb($title, $title);
        $page->getConfig()->getTitle()->prepend($title);
    }
}
