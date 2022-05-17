<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;

class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_BannerSlider::sliders_slider';

    /**
     * @var \Amasty\BannerSlider\Model\Repository\SliderRepository
     */
    private $sliderRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        \Amasty\BannerSlider\Model\Repository\SliderRepository $sliderRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->sliderRepository = $sliderRepository;
        $this->logger = $logger;
    }

    public function execute()
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $sliderId = (int)$this->getRequest()->getParam('id');
        if ($sliderId) {
            try {
                $this->sliderRepository->deleteById($sliderId);
                $this->messageManager->addSuccessMessage(__('The slider have been deleted.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete slider right now. Please review the log and try again.')
                );
                $this->logger->critical($e);

                return $redirect->setPath('*/*/edit', ['id' => $sliderId]);
            }
        }

        return $redirect->setPath('*/*');
    }
}
