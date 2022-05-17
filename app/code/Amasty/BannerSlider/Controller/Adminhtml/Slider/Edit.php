<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Slider;

use Amasty\BannerSlider\Model\Slider;
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
    public const ADMIN_RESOURCE = 'Amasty_BannerSlider::sliders_slider';

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Amasty\BannerSlider\Api\SliderRepositoryInterface
     */
    private $sliderRepository;

    /**
     * @var \Amasty\BannerSlider\Model\SliderFactory
     */
    private $sliderFactory;

    public function __construct(
        Action\Context $context,
        \Amasty\BannerSlider\Api\SliderRepositoryInterface $sliderRepository,
        \Amasty\BannerSlider\Model\SliderFactory $sliderFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->dataPersistor = $dataPersistor;
        $this->sliderRepository = $sliderRepository;
        $this->sliderFactory = $sliderFactory;
    }

    public function execute()
    {
        $sliderId = (int)$this->getRequest()->getParam('id');
        if ($sliderId) {
            try {
                $storeId = (int) $this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);
                $model = $this->sliderRepository->getById($sliderId, $storeId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Slider no longer exists.'));

                return $this->getRedirect('*/*/index');
            }
        } else {
            /** @var Slider $model */
            $model = $this->sliderFactory->create();
        }

        // set entered data if was error when we do save
        $data = $this->dataPersistor->get(Slider::PERSIST_NAME);
        if (!empty($data) && !$model->getId()) {
            $model->addData($data);
        }
        $this->coreRegistry->register(Slider::PERSIST_NAME, $model);

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

    private function updateTitles(Page $page, Slider $model): void
    {
        $title = $model->getId() ?
            __('Edit Slider # %1', $model->getId())
            : __('New Slider');
        $page->addBreadcrumb($title, $title);
        $page->getConfig()->getTitle()->prepend($title);
    }
}
