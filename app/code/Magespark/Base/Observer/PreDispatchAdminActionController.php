<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */


namespace MageSpark\Base\Observer;

use Magento\Framework\Event\ObserverInterface;
use MageSpark\Base\Model\FeedFactory;
use Magento\Backend\Model\Auth\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;

class PreDispatchAdminActionController implements ObserverInterface
{
    /**
     * @var FeedFactory
     */
    private $feedFactory;

    /**
     * @var Session
     */
    private $backendSession;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PreDispatchAdminActionController constructor.
     *
     * @param FeedFactory $feedFactory
     * @param Session $backendAuthSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        FeedFactory $feedFactory,
        Session $backendAuthSession,
        LoggerInterface $logger
    ) {
        $this->feedFactory = $feedFactory;
        $this->backendSession = $backendAuthSession;
        $this->logger = $logger;
    }

    /**
     * Initilize expiry item depend on login user
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->backendSession->isLoggedIn()) {
            try {
                /** @var \MageSpark\Base\Model\Feed $feedModel */
                $feedModel = $this->feedFactory->create();

                $feedModel->checkUpdate();
                $feedModel->removeExpiredItems();
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
            }
        }
    }
}
