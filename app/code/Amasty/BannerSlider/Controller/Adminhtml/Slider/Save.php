<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Slider;

use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\Slider;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_BannerSlider::sliders_slider';
    
    public const DEFAULT_PAUSE_TIME = 3500;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $dataObject;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\BannerSlider\Model\Repository\SliderRepository
     */
    private $sliderRepository;

    /**
     * @var \Amasty\BannerSlider\Model\SliderFactory
     */
    private $sliderFactory;

    public function __construct(
        Action\Context $context,
        \Amasty\BannerSlider\Model\Repository\SliderRepository $sliderRepository,
        \Amasty\BannerSlider\Model\SliderFactory $sliderFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\DataObject $dataObject,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->dataObject = $dataObject;
        $this->logger = $logger;
        $this->sliderRepository = $sliderRepository;
        $this->sliderFactory = $sliderFactory;
    }

    public function execute()
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data = $this->getRequest()->getPostValue()) {
            /** @var Slider $model */
            $model = $this->sliderFactory->create();

            try {
                if ($sliderId = (int) $this->getRequest()->getParam('id')) {
                    $model = $this->sliderRepository->getById($sliderId);
                }

                $data = $this->prepareData($data);
                $model->setData($data);
                $this->sliderRepository->save($model);

                $this->messageManager->addSuccessMessage(__('The Slider was successfully saved.'));
                $this->dataPersistor->clear(Slider::PERSIST_NAME);

                if ($this->getRequest()->getParam('back')) {
                    return $redirect->setPath('ambannerslider/*/edit', [
                        'id' => $model->getId(),
                        'store' => (int)$this->_request->getParam('store_id', Store::DEFAULT_STORE_ID)
                    ]);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($sliderId) {
                    $redirect->setPath('ambannerslider/*/edit', ['id' => $sliderId]);
                } else {
                    $redirect->setPath('ambannerslider/*/newAction');
                }

                return $redirect;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the slider data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->dataPersistor->set(Slider::PERSIST_NAME, $data);

                return $redirect->setPath('ambannerslider/*/edit', ['id' => $sliderId]);
            }
        }

        return $redirect->setPath('ambannerslider/*/');
    }

    private function prepareData(array $data): array
    {
        if (isset($data[SliderInterface::ID])) {
            $data[SliderInterface::ID] = (int) $data[SliderInterface::ID] ?: null;
        }
        if (!isset($data[SliderInterface::BANNERS])) {
            $data[SliderInterface::BANNERS] = [SliderInterface::BANNER_DATA => []];
        }
        if (empty($data[SliderInterface::PAUSE_TIME])) {
            $data[SliderInterface::PAUSE_TIME] = self::DEFAULT_PAUSE_TIME;
        }

        return $data;
    }
}
