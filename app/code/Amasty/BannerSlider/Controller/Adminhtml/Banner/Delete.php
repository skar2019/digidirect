<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Adminhtml\Banner;

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
    public const ADMIN_RESOURCE = 'Amasty_BannerSlider::banners_banner';

    /**
     * @var \Amasty\BannerSlider\Model\Repository\BannerRepository
     */
    private $bannerRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        \Amasty\BannerSlider\Model\Repository\BannerRepository $bannerRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->bannerRepository = $bannerRepository;
        $this->logger = $logger;
    }

    public function execute()
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $bannerId = (int)$this->getRequest()->getParam('id');
        if ($bannerId) {
            try {
                $this->bannerRepository->deleteById($bannerId);
                $this->messageManager->addSuccessMessage(__('The banner have been deleted.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete banner right now. Please review the log and try again.')
                );
                $this->logger->critical($e);

                return $redirect->setPath('*/*/edit', ['id' => $bannerId]);
            }
        }

        return $redirect->setPath('*/*');
    }
}
